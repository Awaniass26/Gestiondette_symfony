<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ClientRepository;

class ClientController extends AbstractController
{
    private ClientRepository $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    #[Route('/client', name: 'app_client')]
    public function list(ClientRepository $repository): Response
{
    $clients = $repository->findAll();

    return $this->render('client/index.html.twig', [
        'clients' => $clients,
    ]);}
}
