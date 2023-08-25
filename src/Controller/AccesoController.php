<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\UserFormType;
use App\Entity\Usuario;


use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Bundle\SecurityBundle\Security;
use App\Form\LoginType;
class AccesoController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/acceso/login', name: 'acceso_login')]
    public function login(Request $request,Security $security, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher): Response
    {
        $entity = new Usuario();
        $form = $this->createForm(LoginType::class, $entity);
        $form->handleRequest($request);
        $submiteddToken = $request->request->get('token');
        if($form->isSubmitted())
        {
            if($this->isCsrfTokenValid('generico',$submiteddToken))
            {
                $errors = $validator->validate($entity);
                if(count($errors) > 0)
                {
                    return $this->render('acceso/login_normal.html.twig', ['form' => $form, 'errors' => $errors]);

                }
                else{
                    $campos = $form->getData();
                    $user = $this->em->getRepository(Usuario::class)->findOneBy([
                        'email'=>$campos->getEmail()
                    ]);

                    if(!$user){
                        $this->addFlash('css','danger');
                        $this->addFlash('mensaje','Las datos son incorrectos');
                        return $this->redirectToRoute('acceso_login');
                    }

                    if($passwordHasher->isPasswordValid($user, $campos->getPassword())){
                        $security->login($user);
                        return $this->redirectToRoute('restringido_inicio');
                    }else{
                        $this->addFlash('css','danger');
                        $this->addFlash('mensaje','Las datos son incorrectos');
                        return $this->redirectToRoute('acceso_login');
                    }
                }

            }else{
                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');
                return $this->redirectToRoute('acceso_login');
            }
        }
    
        return $this->render('acceso/login_normal.html.twig', ["form"=> $form, "errors"=>array()]);
    }

    // en este método la autenticacion se encarga symfony
    // para poder utilizar este método descomentar check_path: acceso_login en security.yaml
    // #[Route('/acceso/login', name: 'acceso_login')]
    // public function login(AuthenticationUtils $authenticationUtils): Response
    // {
    //     $error = $authenticationUtils->getLastAuthenticationError();
    //     $last_username = $authenticationUtils->getLastUsername();        
    //     return $this->render('acceso/login.html.twig', compact('error','last_username'));
    // }

    #[Route('/acceso/registro', name: 'acceso_registro')]
    public function registro(Request $request, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher): Response
    {
        // contraseña ejemplo válida: p2gHNiENUw
        $entity = new Usuario();
        $form = $this->createForm(UserFormType::class, $entity);
        $form->handleRequest($request);
        $submiteddToken = $request->request->get('token');

        if($form->isSubmitted())
        {
            if($this->isCsrfTokenValid('generico',$submiteddToken))
            {
                $errors = $validator->validate($entity);
                if(count($errors) > 0)
                {
                    return $this->render('acceso/registro.html.twig', ['form' => $form, 'errors' => $errors]);
                }else{

                    $campos = $form->getData();
                    $existe = $this->em->getRepository(Usuario::class)->findOneBy([
                        'email'=>$campos->getEmail()
                    ]);

                    if($existe){
                        $this->addFlash('css','danger');
                        $this->addFlash('mensaje','El usuario ya existe');
                        return $this->redirectToRoute('acceso_registro');
                    }
                    $entity->setNombre($campos->getNombre());
                    $entity->setEmail($campos->getEmail());
                    //crear hash de contraseña
                    $entity->setPassword($passwordHasher->hashPassword($entity, $campos->getPassword()));
                    $entity->setRoles(['ROLE_USER']);

                    $this->em->persist($entity);
                    $this->em->flush();
                    $this->addFlash('css','success');
                    $this->addFlash('mensaje','Se creó con éxito el usuario');
                    return $this->redirectToRoute('acceso_registro');
                    
                }

            }else{
                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');
                return $this->redirectToRoute('acceso_registro');
            }
        }

        return $this->render('acceso/registro.html.twig', ['form' => $form, 'errors' => array()]);
    }
    #[Route('/acceso/logout', name: 'acceso_logout')]
    public function acceso_logout(){

    }

}
