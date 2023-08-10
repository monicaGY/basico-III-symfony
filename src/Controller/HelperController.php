<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utilidades\Saludo;
class HelperController extends AbstractController
{
    #[Route('/helper', name: 'app_helper')]
    public function index(): Response
    {
        return $this->render('helper/index.html.twig', [
            'controller_name' => 'HelperController',
        ]);
    }

    #[Route('/helper/saludo', name: 'helper_saludo')]
    public function saludo(): Response
    {   //para metodos estáticos
        $saludo = Saludo::saludar('Juan Alfredo');
        $s = new Saludo();
        $saludo2 = $s->saludar2('Elena Fernanda');
       
        return $this->render('helper/index.html.twig',compact('saludo','saludo2'));
    }
}
