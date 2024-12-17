<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{
    #[Route('/boutiquier/article/{filter}', name: 'app_article', defaults: ['filter' => 'all'])]
    public function index(ArticleRepository $articleRepository, Request $request, string $filter): Response
    {
        $search = $request->query->get('search', ''); 
        $page = (int) $request->query->get('page', 1); 
        $limit = 8;

        if ($filter === 'rup') {
            $articles = $articleRepository->findRupture(); 
        } elseif ($filter === 'dis') {
            $articles = $articleRepository->findAvailable(); 
        } else {
            if (!empty($search)) {
                $articles = $articleRepository->findByLibelle($search); 
            } else {
                $articles = $articleRepository->findAllArticles(); 
            }
        }

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
