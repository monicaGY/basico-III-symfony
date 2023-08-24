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

class AccesoController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/acceso/login', name: 'acceso_login')]
    public function login(): Response
    {
        return $this->render('acceso/login.html.twig');
    }

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
}
