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



    #[Route('/save-selected-products', name: 'app_save_selected_products', methods:"POST")]

    public function saveSelectedProducts(Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);
        $selectedProducts = $data['products'];

        foreach ($selectedProducts as $productData) {
            $product = new Article();
            $product->setNomArticle($productData['name']);
            $product->setPrix($productData['price']);
            $product->setQuantiteRestante($productData['quantity']);

            $em->persist($product);
        }

        $em->flush();

        $updatedProducts = $em->getRepository(Article::class)->findAll();

        return new JsonResponse([
            'success' => true,
            'updatedProducts' => $updatedProducts,
        ]);
    }
}
