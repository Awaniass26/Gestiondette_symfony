<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // Import de Request
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

    #[Route('/boutiquier/client', name: 'app_client')]
public function list(Request $request): Response
{
    $phone = $request->query->get('phone', ''); 
    $page = $request->query->getInt('page', 1);
    $limit = 6;

    if (!empty($phone)) {
        $paginator = $this->clientRepository->findByPhone($phone, $page, $limit);
    } else {
        $paginator = $this->clientRepository->findClientsPaginated($page, $limit);
    }

    $totalItems = count($paginator);
    $pagesCount = ceil($totalItems / $limit);

    return $this->render('client/index.html.twig', [
        'clients' => $paginator,
        'totalItems' => $totalItems,
        'pagesCount' => $pagesCount,
        'currentPage' => $page,
        'phone' => $phone, 
    ]);
}

}
