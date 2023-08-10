<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{

    #[Route('/post/{id}', name: 'post')]
    public function index(int $id): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_nam' => 'PostController!',
            'array_asociativo' => ['despedida' => 'Adioos', 'nombre' => 'Esteban'],
            'id' => $id
        ]);
    }
}
