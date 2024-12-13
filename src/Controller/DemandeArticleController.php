<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DemandeArticleController extends AbstractController
{
    #[Route('/demande/article', name: 'app_demande_article')]
    public function index(): Response
    {
        return $this->render('demande_article/index.html.twig', [
            'controller_name' => 'DemandeArticleController',
        ]);
    }
}
