<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;


class ClientController extends AbstractController
{
    private ClientRepository $clientRepository;

    public function __construct(ClientRepository $clientRepository,EntityManagerInterface $entityManager)
    {
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;

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

    if ($request->isXmlHttpRequest()) {
        $phone = $request->query->get('phone', '');
        $clients = $this->clientRepository->findClientsPaginated(1, 100); 

        $clientData = [];
        foreach ($clients as $client) {
            $clientData[] = [
                'nom' => $client->getNom(),
                'prenom' => $client->getPrenom(),
                'telephone' => $client->getTelephone(),
                'adresse' => $client->getAdresse(),
            ];
        }

        return $this->json(['clients' => $clientData]);
    }

    return $this->render('client/index.html.twig', [
        'clients' => $paginator,
        'totalItems' => $totalItems,
        'pagesCount' => $pagesCount,
        'currentPage' => $page,
        'phone' => $phone, 
    ]);
}



#[Route('/boutiquier/client', name: 'create_client', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (empty($data['nom']) || empty($data['prenom']) || empty($data['telephone']) || empty($data['adresse'])) {
            return $this->json(['success' => false, 'message' => 'Tous les champs doivent être remplis']);
        }

        $client = new Client();
        $client->setNom($data['nom']);
        $client->setPrenom($data['prenom']);
        $client->setTelephone($data['telephone']);
        $client->setAdresse($data['adresse']);

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'client' => [
                'nom' => $client->getNom(),
                'prenom' => $client->getPrenom(),
                'telephone' => $client->getTelephone(),
                'adresse' => $client->getAdresse(),
            ]
        ]);
    }
}
