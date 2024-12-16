<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{
    #[Route('/article/{filter}', name: 'app_article', defaults: ['filter' => 'all'])]
public function index(ArticleRepository $articleRepository, Request $request, string $filter): Response
{
    $search = $request->query->get('search', ''); 
    $page = (int) $request->query->get('page', 1); 
    $limit = 8;

    // Appliquer le filtre selon la valeur de $filter
    if ($filter === 'rup') {
        $articles = $articleRepository->findRupture(); // Articles en rupture
    } elseif ($filter === 'dis') {
        $articles = $articleRepository->findAvailable(); // Articles disponibles
    } else {
        $articles = $articleRepository->findAllArticles(); // Tous les articles
    }

    // Pagination manuelle (si nécessaire)
    $totalArticles = count($articles);
    $totalPages = ceil($totalArticles / $limit);
    $offset = ($page - 1) * $limit;
    $articlesPaginated = array_slice($articles, $offset, $limit);

    return $this->render('article/index.html.twig', [
        'articles' => $articlesPaginated, 
        'filter' => $filter,
        'search' => $search,
        'currentPage' => $page,
        'totalPages' => $totalPages,
    ]);
}

}
