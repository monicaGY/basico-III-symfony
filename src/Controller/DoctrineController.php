<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Categoria;
use Doctrine\ORM\EntityManagerInterface;

//formulario añadir
use App\Form\CategoriaFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
//procesar la información
use Symfony\Component\String\Slugger\SluggerInterface;

class DoctrineController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/doctrine', name: 'inicio_doctrine')]
    public function index(): Response
    {
        return $this->render('doctrine/index.html.twig');
    }

    #[Route('/doctrine/categorias', name: 'doctrine_categoria')]
    // public function categorias(EntityManagerInterface $em): Response
    public function categorias(): Response
    {
        // obtener datos
        // $datos = $em->getRepository(Categoria::class)->findAll();
        
        //findBy(filtro, configuraciones)
        $datos = $this->em->getRepository(Categoria::class)->findBy(array(),array('id'=>'desc'));

        return $this->render('doctrine/categorias.html.twig',compact('datos'));
    }

    #[Route('/doctrine/categorias/add', name: 'doctrine_añadir')]
    public function añadir(Request $request, ValidatorInterface $validator,SluggerInterface $slugger): Response
    {
        
        $categoria = new Categoria();
        // createForm(formulario, clase)
        $form = $this->createForm(CategoriaFormType::class, $categoria);
        $form->handleRequest($request);
        $submiteddToken = $request->request->get('token');

        if($form->isSubmitted())
        {

            if($this->isCsrfTokenValid('generico',$submiteddToken))
            {
                $errors = $validator->validate($categoria);
                if(count($errors) > 0)
                {
                    return $this->render('doctrine/añadir.html.twig', ['formulario' => $form, 'errors' => $errors]);

                }else{
                    $campos = $form->getData();
                    $categoria->setNombre($campos->getNombre());
                    //crear slug
                    $categoria->setSlug($slugger->slug($campos->getNombre()));

                    //crear, eliminar, insertar
                    $this->em->persist($categoria);
                    //aplicar el cambio
                    $this->em->flush();

                    $this->addFlash('css','success');
                    $this->addFlash('mensaje','Se creo el registro');
                    return $this->redirectToRoute('doctrine_añadir');
                }


            }else{
                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');
                return $this->redirectToRoute('doctrine_añadir');
            }

        }
        return $this->render('doctrine/añadir.html.twig',['formulario'=> $form, 'errors'=> array()]);
    }
}
