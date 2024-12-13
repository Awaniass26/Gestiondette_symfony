<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DemandeController extends AbstractController
{
    #[Route('/demande', name: 'app_demande')]
    public function index(): Response
    {
        return $this->render('demande/index.html.twig', [
            'controller_name' => 'DemandeController',
        ]);
    }

    #[Route('/detaildemande', name: 'app_demande_detail')]
    public function details(): Response
    {
        return $this->render('demande/detaildemande.html.twig', [
            'controller_name' => 'DemandeController',
        ]);
    }
}
