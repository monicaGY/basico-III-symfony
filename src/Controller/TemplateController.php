<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TemplateController extends AbstractController
{
    #[Route('/template', name: 'template')]
    public function index(): Response
    {
        return $this->render('template/index.html.twig', [
            'controller_name' => 'TemplateController',
        ]);
    }

    #[Route('/template/parametro/{id}/{palabra}', name: 'template_parametros', 
    defaults: ['id' => -1])]
    public function parametros(int $id , string $palabra = "vacío"): Response
    {

        if($id < 0 ){
            return $this->redirectToRoute('template_excepcion');

            // return $this->redirect('http://symfony.com/doc');
        }
        
        die("parametro id = {$id}; palabra = {$palabra}");
    }

    #[Route('/template/excepcion', name: 'template_excepcion')]
    public function excepcion(): Response
    {
        // throw $this->createNotFoundException('Esta URL no esta disponible');
        throw new NotFoundHttpException('Esta URL no esta disponible !');
    }
    
    #[Route('/template/trabajo', name: 'template_trabajo')]
    public function trabajo(): Response
     {
        //  $nombre = "Elvia";
        //  $edad = 35;

        //  return $this->render('template/trabajo.html.twig',
        //  compact('nombre', 'edad')
        //  );

        $paises = array (
            array (
                "nombre" => "Alemania", "id" => 1
                ) ,
            array (
                "nombre" => "España", "id" => 2
                ) ,
            array (
                "nombre" => "Francia", "id" => 3
                ) ,
            array (
                "nombre" => "Italia", "id" => 4
                ) ,
            array (
                "nombre" => "Australia", "id" => 5
                ) 
        );
        
         return $this->render('template/trabajo.html.twig', [
             'nombre' => 'Juana', 'edad' => 20,
             'paises' => $paises
         ]);
     }

     #[Route('/template/layout', name: 'template_layout')]
     public function layout(): Response
     {
         return $this->render('template/layout.html.twig');
     }
}
