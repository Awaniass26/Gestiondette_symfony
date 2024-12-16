<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ClientRepository;
use App\Repository\DetteRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Entity\Article;
use App\Entity\Paiement;


class DetteController extends AbstractController
{
    private $clientRepository;
    private $entityManager;
    private $userRepository;
    private $detteRepository;

    public function __construct(ClientRepository $clientRepository, EntityManagerInterface $entityManager, UserRepository $userRepository, DetteRepository $detteRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->detteRepository = $detteRepository;
    }

    #[Route('/dette', name: 'app_dette',methods: ['GET', 'POST']) ]
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $prenom = $request->request->get('prenom', ''); 
            $nom = $request->request->get('nom', ''); 
            $telephone = $request->request->get('telephone', ''); 
            $adresse = $request->request->get('adresse', ''); 
            $photo = $request->files->get('photo');
            $creerCompte = $request->request->get('creerCompte');
            $login = $request->request->get('login');
            $password = $request->request->get('password');
        
            $client = new Client();
            $client->setPrenom($prenom ?? ''); 
            $client->setNom($nom ?? ''); 
            $client->setTelephone($telephone ?? ''); 
            $client->setAdresse($adresse ?? ''); 

            $client->setEmail($prenom . '.' . $nom . '@default.com');
        
            if ($photo) {
                $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/photos';
                $photoName = uniqid() . '.' . $photo->guessExtension();
                $photo->move($uploadsDirectory, $photoName);
            
                $client->setPhoto('uploads/photos/' . $photoName);

            }
            
        
            $this->entityManager->persist($client);
            $this->entityManager->flush();  

            // dd($client);
        
            if ($creerCompte === 'on') {
                $user = new User();
                $user->setUsername($login);
                $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
                $user->setClient($client);  
        
                $this->entityManager->persist($user);
                $this->entityManager->flush(); 
            }
        
            $this->addFlash('success', 'Client créé avec succès !');
        
            return $this->redirectToRoute('app_client');
        }
        

        $client = null;
        $montant = null;
        $date = null;

        if ($request->isMethod('POST') && $request->request->get('telephone')) {
            $telephone = $request->request->get('telephone');
            $client = $this->clientRepository->findOneBy(['telephone' => $telephone]);

            if (!$telephone) {
                $this->addFlash('error', 'Veuillez saisir un numéro de téléphone.');
                return $this->redirectToRoute('app_dette');
            }
    
            $client = $this->clientRepository->findOneBy(['telephone' => $telephone]);
    
            if (!$client) {
                $this->addFlash('error', 'Aucun client trouvé avec ce numéro.');
            } else {
                $dette = $this->detteRepository->findOneBy(['client' => $client]);
                if ($dette) {
                    $montant = $dette->getMontant();
                    $date = $dette->getDateAt();
                }
            }
        }
        

        return $this->render('dette/form.html.twig', [
            'client' => $client,
            'montant' => $montant,
            'date' => $date,
        ]);
    }

    #[Route('/recherche-client', name: 'app_recherche_client', methods: ['POST'])]
public function rechercheClient(Request $request): Response
{
    $telephone = $request->request->get('telephone');
    $client = $this->clientRepository->findOneBy(['telephone' => $telephone]);

    if ($client) {
        return $this->render('dette/form.html.twig', [
            'client' => $client,  
        ]);
    }

    $this->addFlash('error', 'Aucun client trouvé avec ce numéro de téléphone.');
    return $this->redirectToRoute('app_dette');
}


#[Route('/dette/{telephone}', name: 'app_dettelist')]
public function afficherDettes(string $telephone, Request $request): Response
{
    $client = $this->clientRepository->findOneBy(['telephone' => $telephone]);

    if (!$client) {
        $this->addFlash('error', 'Client introuvable.');
        return $this->redirectToRoute('app_dette');
    }

    $type = $request->query->get('type');
    $page = max(1, (int)$request->query->get('page', 1));
    $limit = 8; 
    $offset = ($page - 1) * $limit;

    $qb = $this->detteRepository->createQueryBuilder('d')
        ->where('d.client = :client')
        ->setParameter('client', $client)
        ->setFirstResult($offset)
        ->setMaxResults($limit);

    if ($type === 'soldees') {
        $qb->andWhere('d.montantRestant = 0');
    } elseif ($type === 'non_soldees') {
        $qb->andWhere('d.montantRestant != 0');
    }

    $paginator = new Paginator($qb->getQuery(), true);
    $dettes = $paginator->getIterator(); 
    $totalDettes = count($paginator);
    $totalPages = (int)ceil($totalDettes / $limit);

    $montantTotal = array_reduce($dettes->getArrayCopy(), fn($total, $dette) => $total + $dette->getMontant(), 0);
    $montantVerse = array_reduce($dettes->getArrayCopy(), fn($total, $dette) => $total + $dette->getMontantVerse(), 0);
    $montantRestant = $montantTotal - $montantVerse;

    return $this->render('dette/list.html.twig', [
        'client' => $client,
        'dettes' => $dettes,
        'montantTotal' => $montantTotal,
        'montantVerse' => $montantVerse,
        'montantRestant' => $montantRestant,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'type' => $type,
    ]);
}





#[Route('/dette/details/{id}', name: 'app_detail_dette')]
public function detailDette(int $id, Request $request, EntityManagerInterface $entityManager): Response
{
    $dette = $this->detteRepository->find($id);

    if (!$dette) {
        $this->addFlash('error', 'Aucune dette trouvée.');
        return $this->redirectToRoute('app_dette');
    }

    $client = $dette->getClient();

    $montantTotal = $dette->getMontant();
    $montantVerse = $dette->getMontantVerse();
    $montantRestant = $montantTotal - $montantVerse;

    $page = $request->query->getInt('page', 1);
    $limit = 8; 
    $query = $entityManager->getRepository(Article::class)
            ->createQueryBuilder('a')
            ->getQuery();

    $paginator = new Paginator($query);
    $totalArticles = count($paginator);
    $articles = iterator_to_array($paginator->getIterator());
    $articles = array_slice($articles, ($page - 1) * $limit, $limit);
    $totalPages = ceil($totalArticles / $limit);


    $pagePaiement = $request->query->getInt('page_paiement', 1);
    $limitPaiement = 6; 
    $query = $entityManager->getRepository(Paiement::class)
        ->createQueryBuilder('p')
        ->where('p.dette = :dette')
        ->setParameter('dette', $dette)
        ->orderBy('p.dateAt', 'DESC')
        ->getQuery();

        $paginatorPaiement = new Paginator($query);
        $totalPaiements = count($paginatorPaiement);
        $paiements = iterator_to_array($paginatorPaiement->getIterator());
        $paiements = array_slice($paiements, ($pagePaiement - 1) * $limitPaiement, $limitPaiement);
        $totalPagesPaiement = ceil($totalPaiements / $limitPaiement);

    return $this->render('dette/detail.html.twig', [
        'dette' => $dette,
        'client' => $client,
        'montantTotal' => $montantTotal,
        'montantVerse' => $montantVerse,
        'montantRestant' => $montantRestant,
        'articles' => $articles,
        'totalArticles' => $totalArticles,
        'totalPages' => $totalPages,
        'page' => $page,
        'paiements'=>$paiements,
        'totalPaiements' => $totalPaiements,
        'totalPagesPaiement' => $totalPagesPaiement,
        'pagePaiement' => $pagePaiement,
    ]);
}


#[Route('/dette/nouvelle', name: 'app_nouvelle_dette', methods: ['GET', 'POST'])]
public function newDette(Request $request, EntityManagerInterface $entityManager): Response
{
    $articles = $entityManager->getRepository(Article::class)->createQueryBuilder('a')
        ->where('a.quantite > 0')
        ->getQuery()
        ->getResult();

    $client = $this->getClientById($request->query->getInt('client_id'), $entityManager);

    if (!$client) {
        $this->addFlash('error', 'Client introuvable.');
        return $this->redirectToRoute('app_nouvelle_dette');
    }

    if ($request->isMethod('POST')) {
        $selectedArticles = $request->request->get('article', []);
        $montantTotal = 0;

        foreach ($selectedArticles as $articleId) {
            $article = $entityManager->getRepository(Article::class)->find($articleId);
            if ($article) {
                $montantTotal += $article->getPrix();
            }
        }

        if ($montantTotal > 0) {
            $dette = new Dette();
            $dette->setClient($client);
            $dette->setMontant($montantTotal);
            $dette->setMontantVerse(0);
            $entityManager->persist($dette);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvelle dette créée avec succès.');
            return $this->redirectToRoute('app_nouvelle_dette'); 
        }

        $this->addFlash('error', 'Aucun article sélectionné ou montant invalide.');
    }

    return $this->render('dette/newdette.html.twig', [
        'client' => $client,
        'articles' => $articles,
    ]);
}

private function getClientById(int $clientId, EntityManagerInterface $entityManager): ?Client
{
    return $entityManager->getRepository(Client::class)->find($clientId);
}





}
    