<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Persona;
use App\Entity\PersonaEntity;
use App\Form\PersonEntityFormType;
use App\Entity\PersonaEntityValidation;
use App\Form\PersonaValidationType;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Entity\PersonaEntityUpload;
use App\Form\PersonaUploadType;
class FormulariosController extends AbstractController
{
    #[Route('/formularios', name: 'form_inicio')]
    public function index(): Response
    {
        return $this->render('formularios/index.html.twig', [
            'controller_name' => 'FormulariosController',
        ]);
    }

    #[Route('/formularios/simple', name: 'form_simple')]
    public function simple(Request $request): Response
    {
        $formulario = $this->createFormBuilder(null)
            ->add('nombre', TextType::class, ['label' => 'Nombre'])
            ->add('correo', TextType::class, ['label' => 'Email'])
            ->add('telefono', TextType::class, ['label' => 'Telefono'])
            ->add('save', SubmitType::class,)
            ->getForm();

        

        $submiteddToken= $request->request->get('token');
        $formulario->handleRequest($request);
        if($formulario->isSubmitted())
        {
            if($this->isCsrfTokenValid('generico',$submiteddToken))
            {
                $campos = $formulario->getData();
                echo 'Nombre: '. $campos["nombre"];
                echo '<br>Correo: '. $campos["correo"];
                echo '<br>Teléfono: '. $campos["telefono"];
                die();
            }
            else
            {

                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');
                return $this->redirectToRoute('form_simple');

            }
            
        }


        return $this->render('formularios/simple.html.twig',compact('formulario'));
    }


    #[Route('/formularios/entity', name: 'form_entity')]
    public function entity(Request $request): Response
    {
        $persona = new Persona();

        $formulario = $this->createFormBuilder($persona)
            ->add('nombre', TextType::class, ['label' => 'Nombre'])
            ->add('correo', TextType::class, ['label' => 'Email'])
            ->add('telefono', TextType::class, ['label' => 'Telefono'])
            ->add('save', SubmitType::class,)
            ->getForm();

        

        $submiteddToken= $request->request->get('token');
        $formulario->handleRequest($request);
        if($formulario->isSubmitted())
        {
            if($this->isCsrfTokenValid('generico',$submiteddToken))
            {
                $campos = $formulario->getData();
                echo 'Nombre: '. $campos->getNombre();
                echo '<br>Correo: '. $campos->getCorreo();
                echo '<br>Teléfono: '. $campos->getTelefono();
                die();
            }
            else
            {

                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');
                return $this->redirectToRoute('form_entity');

            }
            
        }
        return $this->render('formularios/entity.html.twig',compact('formulario'));
    }


    #[Route('/formularios/typeForm', name: 'form_typeForm')]
    public function typeForm(Request $request): Response
    {
        
        $persona = new PersonaEntity();
        $formulario = $this->createForm(PersonEntityFormType::class, $persona);

        

        $submiteddToken= $request->request->get('token');
        $formulario->handleRequest($request);
        if($formulario->isSubmitted())
        {
            if($this->isCsrfTokenValid('generico',$submiteddToken))
            {
                $campos = $formulario->getData();
                echo 'Nombre: '. $campos["nombre"];
                echo '<br>Correo: '. $campos["correo"];
                echo '<br>Teléfono: '. $campos["telefono"];
                die();
            }
            else
            {

                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');
                return $this->redirectToRoute('form_simple');

            }
            
        }
        return $this->render('formularios/typeForm.html.twig', compact('formulario'));
    }
    
    #[Route('/formularios/validacion', name: 'form_validacion')]
    public function validacion(Request $request, ValidatorInterface $validator): Response
    {
        $persona = new PersonaEntityValidation();
        $form = $this -> createForm(PersonaValidationType::class, $persona);
        
        $form->handleRequest($request);
        $submiteddToken = $request->request->get('token');

        if($form->isSubmitted())
        {

            if($this->isCsrfTokenValid('generico',$submiteddToken))
            {
                $errors = $validator->validate($persona);
                if(count($errors) > 0)
                {
                    // echo 'alert('.count($errors).')';
                    return $this->render('formularios/validacion.html.twig', ['formulario' => $form, 'errors' => $errors]);

                }else
                {
                    $campos = $form->getData();
                    echo 'Nombre: '. $campos->getNombre();
                    echo '<br>Correo: '. $campos->getCorreo();
                    echo '<br>Teléfono: '. $campos->getTelefono();
                    die();
                }
            }else
            {
                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');
                return $this->redirectToRoute('form_validacion');
            }
        }
        return $this->render('formularios/validacion.html.twig', ['formulario' => $form, 'errors' => []]);
    }


    #[Route('/formularios/upload', name: 'form_upload')]
    public function upload(Request $request, ValidatorInterface $validator): Response
    {
        $persona = new PersonaEntityUpload();
        $form = $this -> createForm(PersonaUploadType::class, $persona);
        
        $form->handleRequest($request);
        $submiteddToken = $request->request->get('token');

        if($form->isSubmitted())
        {

            if($this->isCsrfTokenValid('generico',$submiteddToken))
            {
                $errors = $validator->validate($persona);
                if(count($errors) > 0)
                {
                    // echo 'alert('.count($errors).')';
                    return $this->render('formularios/upload.html.twig', ['formulario' => $form, 'errors' => $errors]);

                }else
                {
                    $foto = $form -> get('foto')->getData();
                    if($foto)
                    {
                        $originalName = pathinfo($foto->getClientOriginalName(), PATHINFO_FILENAME);

                        $newfilename = time().'.'.$foto->guessExtension();
                        try{
                            $foto->move(
                                $this->getParameter('fotos_directory'),
                                $newfilename
                            );

                        }catch(FileException $th){
                            throw new Exception("mensaje", "Ocurrió un error intentalo más tarde");
                            
                        }

                        $persona->setFoto($newfilename);
                    }

                    $campos = $form->getData();
                    echo 'Nombre: '. $campos->getNombre();
                    echo '<br>Correo: '. $campos->getCorreo();
                    echo '<br>Teléfono: '. $campos->getTelefono();

                    echo '<br>Foto: '. $campos->getFoto();
                    die();
                }
            }else
            {
                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');
                return $this->redirectToRoute('form_upload');
            }
        }
        return $this->render('formularios/upload.html.twig', ['formulario' => $form, 'errors' => []]);
        
    }
}
