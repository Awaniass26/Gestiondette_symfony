<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ClientRepository;
use App\Repository\ArticleRepository;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_dashboard')]
    public function index(ClientRepository $clientRepository, ArticleRepository $articleRepository, Request $request): Response
    {
        $pageClients = $request->query->getInt('page_clients', 1);
        $limitClients = 5;  
        $clients = $clientRepository->findBy([], null, $limitClients, ($pageClients - 1) * $limitClients);

        $pageArticles = $request->query->getInt('page_articles', 1);
        $limitArticles = 5;  
        $articles = $articleRepository->findBy([], null, $limitArticles, ($pageArticles - 1) * $limitArticles);

        $totalClients = $clientRepository->count([]);
        $totalArticles = $articleRepository->count([]);

        $nbrePagesClients = ceil($totalClients / $limitClients);
        $nbrePagesArticles = ceil($totalArticles / $limitArticles);

        return $this->render('dashboard/index.html.twig', [
            'clients' => $clients,
            'articles' => $articles,
            'nbrePagesClients' => $nbrePagesClients,
            'nbrePagesArticles' => $nbrePagesArticles,
            'pageClients' => $pageClients,
            'pageArticles' => $pageArticles,
        ]);
    }
}
