<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Demande;
use App\Entity\DemandeArticle;


class DemandeController extends AbstractController
{
    #[Route('/demande', name: 'app_demande')]
public function index(Request $request, EntityManagerInterface $entityManager): Response
{
    $status = $request->query->get('status', 'En cours'); 
    $page = $request->query->getInt('page', 1); 
    $limit = 8; 

    $demandesQuery = $entityManager->getRepository(Demande::class)
        ->createQueryBuilder('d')
        ->where('d.statut = :status')
        ->setParameter('status', $status)
        ->orderBy('d.dateAt', 'DESC')
        ->getQuery();

    $totalDemandes = count($demandesQuery->getResult());
    $demandes = $demandesQuery
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit)
        ->getResult();

    $totalPages = ceil($totalDemandes / $limit);

    return $this->render('demande/index.html.twig', [
        'demandes' => $demandes,
        'status' => $status,
        'currentPage' => $page,
        'totalPages' => $totalPages,
    ]);
}


#[Route('/detaildemande/{id}', name: 'app_demande_detail')]
public function details(int $id, EntityManagerInterface $entityManager,Request $request): Response
{
    $demande = $entityManager->getRepository(Demande::class)->find($id);

    if (!$demande) {
        throw $this->createNotFoundException('Demande non trouvée');
    }

    $limit = 8; 
    $page = (int)$request->query->get('page', 1); 

    $offset = ($page - 1) * $limit;

    $status = 'En cours';

    $demandesQuery = $entityManager->getRepository(Demande::class)
        ->createQueryBuilder('d')
        ->where('d.statut = :status')
        ->setParameter('status', $status)
        ->orderBy('d.dateAt', 'DESC')
        ->getQuery();

        $demandes = $demandesQuery->getResult(); 


    $montantTotal = $demande->getMontant();
    $montantVerse = 0; 
    $montantRestant = $montantTotal - $montantVerse;

    foreach ($demandes as $demande) {
        foreach ($demande->getDemandeArticles() as $demandeArticle) {
            $montantTotal += $demandeArticle->getQuantite() * $demandeArticle->getArticle()->getPrix();
        }
        
        $montantVerse += $demande->getMontant();
        $montantRestant += $montantTotal - $montantVerse;
    }

    $articles = $entityManager->getRepository(DemandeArticle::class)
    ->findBy(['demande' => $demande], null, $limit, $offset);

    $totalArticles = $entityManager->getRepository(DemandeArticle::class)
    ->count(['demande' => $demande]);

    $totalPages = ceil($totalArticles / $limit);


    return $this->render('demande/detaildemande.html.twig', [
        'demande' => $demande,
        'client' => $demande->getClient(), 
        'articles' => $articles,
        'montant' => $montantTotal,
        'montantVerse' => $montantVerse,
        'montantRestant' => $montantRestant,
        'currentPage' => $page,
        'totalPages' => $totalPages,
    ]);
}

}
