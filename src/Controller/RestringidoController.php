<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestringidoController extends AbstractController
{
    #[Route('/restringido', name: 'restringido_inicio')]
    public function index(): Response
    {
        return $this->render('restringido/index.html.twig');
    }
}
