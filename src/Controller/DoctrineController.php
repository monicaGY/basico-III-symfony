<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Categoria;
use Doctrine\ORM\EntityManagerInterface;

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
}
