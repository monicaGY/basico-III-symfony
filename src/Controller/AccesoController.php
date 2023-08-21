<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccesoController extends AbstractController
{
    #[Route('/acceso/login', name: 'acceso_login')]
    public function login(): Response
    {
        return $this->render('acceso/login.html.twig');
    }

    #[Route('/acceso/registro', name: 'acceso_registro')]
    public function registro(): Response
    {
        return $this->render('acceso/registro.html.twig');
    }
}
