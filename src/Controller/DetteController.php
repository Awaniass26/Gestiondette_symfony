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
        
            if ($photo) {
                $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/photos';
                $photoName = uniqid() . '.' . $photo->guessExtension();
                $photo->move($uploadsDirectory, $photoName);
            
                $client->setPhoto('uploads/photos/' . $photoName);

                dd($client->getPhoto());

            }
            
        
            $this->entityManager->persist($client);
            $this->entityManager->flush();  
        
            if ($creerCompte === 'on') {
                $user = new User();
                $user->setUsername($login);
                $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
                $user->setClient($client);  
        
                $this->entityManager->persist($user);
                $this->entityManager->flush();  
            }
        
            $this->addFlash('success', 'Client créé avec succès !');
        
            return $this->redirectToRoute('app_dette');
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

    #[Route('/regler-dette/{clientId}', name: 'app_regler_dette')]
    public function reglerDette(int $clientId): Response
    {
        $client = $this->clientRepository->find($clientId);
        $dette = $this->detteRepository->findOneBy(['client' => $client]);

        if ($dette) {
            $this->detteService->reglerDette($dette);

            $this->addFlash('success', 'La dette a été réglée avec succès !');
        } else {
            $this->addFlash('error', 'Aucune dette associée à ce client.');
        }

        return $this->redirectToRoute('app_dette');
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
    $qb = $this->detteRepository->createQueryBuilder('d')
        ->where('d.client = :client')
        ->setParameter('client', $client);
    
    if ($type === 'soldees') {
        $qb->andWhere('d.montantRestant = 0');
    } elseif ($type === 'non_soldees') {
        $qb->andWhere('d.montantRestant != 0');
    }
    
    $dettes = $qb->getQuery()->getResult();
    
    $montantTotal = array_reduce($dettes, fn($total, $dette) => $total + $dette->getMontant(), 0);
    $montantVerse = array_reduce($dettes, fn($total, $dette) => $total + $dette->getMontantVerse(), 0);
    $montantRestant = $montantTotal - $montantVerse;

    dump($dettes);

    return $this->render('dette/list.html.twig', [
        'client' => $client,
        'dettes' => $dettes,
        'montantTotal' => $montantTotal,
        'montantVerse' => $montantVerse,
        'montantRestant' => $montantRestant,
    ]);
}



}
    