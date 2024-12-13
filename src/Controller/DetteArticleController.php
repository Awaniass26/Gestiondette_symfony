<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DetteArticleController extends AbstractController
{
    #[Route('/dette/article', name: 'app_dette_article')]
    public function index(): Response
    {
        return $this->render('dette_article/index.html.twig', [
            'controller_name' => 'DetteArticleController',
        ]);
    }
}
