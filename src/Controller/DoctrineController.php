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


use App\Entity\Producto;
//paginacion
use Knp\Component\Pager\PaginatorInterface;
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

    #[Route('/doctrine/categoria/editar/{id}', name: 'doctrine_categoria_editar')]
    public function categoria_editar(Request $request, int $id, ValidatorInterface $validator,SluggerInterface $slugger): Response
    {
        //select * from categoria c where c.id = {id}
        $categoria = $this->em->getRepository(Categoria::class)->find($id);

        if(!$categoria){
            throw $this->createNotFoundException(
                "Esta url no esta disponible"
            );
        }

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
                    return $this->render('doctrine/categoria_editar.html.twig', ['formulario' => $form, 'errors' => $errors]);

                }else{
                    $campos = $form->getData();
                    $categoria->setNombre($campos->getNombre());
                    //crear slug
                    $categoria->setSlug($slugger->slug($campos->getNombre()));

                    //aplicar el cambio
                    $this->em->flush();

                    $this->addFlash('css','success');
                    $this->addFlash('mensaje','Se modificó el registro');
                    return $this->redirectToRoute('doctrine_categoria_editar', ['id'=> $categoria->getId()]);
                }


            }else{
                $this->addFlash('css','warning');
                $this->addFlash('mensaje','Ocurrió un error inesperado');
                return $this->redirectToRoute('doctrine_categoria_editar');
            }

        }
        return $this->render('doctrine/categoria_editar.html.twig',['formulario'=> $form, 'errors'=> array(), 'categoria'=> $categoria]);
    }
    #[Route('/doctrine/categoria/eliminar/{id}', name: 'doctrine_categoria_eliminar')]
    public function categoria_eliminar(Request $request, int $id, ValidatorInterface $validator,SluggerInterface $slugger): Response
    {
        $categoria = $this->em->getRepository(Categoria::class)->find($id);

        if(!$categoria){
            throw $this->createNotFoundException(
                "Esta url no esta disponible"
            );
        }

        $this->em->remove($categoria);
        $this->em->flush();
        $this->addFlash('css','success');
        $this->addFlash('mensaje','Eliminado correctamente');
        return $this->redirectToRoute('doctrine_categoria');
    }

    #[Route('/doctrine/productos', name: 'doctrine_productos')]
    public function productos(): Response
    {
        // obtener datos
        $datos = $this->em->getRepository(Producto::class)->findAll();

        return $this->render('doctrine/productos.html.twig',compact('datos'));
    }

    #[Route('/doctrine/productos/paginacion', name: 'doctrine_productos_paginacion')]
    public function productos_paginacion(PaginatorInterface $paginator, Request $request): Response
    {
        $datos = $this->em->getRepository(Producto::class)->findAll();
        $paginator = $paginator->paginate($datos,$request->query->getInt('page', 1),
        2);
        return $this->render('doctrine/productos_paginacion.html.twig',compact('datos','paginator'));
    }

    #[Route('/doctrine/productos/categoria/{categoria_id}', name: 'doctrine_productos_categoria')]
    public function productos_categoria(int $categoria_id): Response
    {
        // obtener datos
        $categoria = $this->em->getRepository(Categoria::class)->find($categoria_id);

        if(!$categoria){
            throw $this->createNotFoundException('Esta URL no existe');
        }
        $datos = $this->em->getRepository(Producto::class)->findBy(array('categoria'=>$categoria_id),array('id'=>'desc'));

        return $this->render('doctrine/productos_categoria.html.twig',compact('datos', 'categoria'));
    }
}
