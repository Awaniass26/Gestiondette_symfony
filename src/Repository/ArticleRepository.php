<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findRupture(): array
{
    return $this->createQueryBuilder('a')
        ->where('a.quantiteRestante = 0')
        ->getQuery()
        ->getResult();
}

public function findAvailable(): array
{
    return $this->createQueryBuilder('a')
        ->where('a.quantiteRestante > 0')
        ->getQuery()
        ->getResult();
}

public function findAllArticles(): array
{
    return $this->createQueryBuilder('a')
        ->getQuery()
        ->getResult();
}


    public function findByLibelle(string $search): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.nomArticle LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }


    public function findPaginatedArticles(int $page, int $limit, string $search = ''): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('a');

        if (!empty($search)) {
            $queryBuilder->where('a.nomArticle LIKE :search')
                         ->setParameter('search', '%' . $search . '%');
        }

        $queryBuilder->orderBy('a.nomArticle', 'ASC')
                     ->setFirstResult(($page - 1) * $limit)
                     ->setMaxResults($limit);

        $query = $queryBuilder->getQuery();
        return new Paginator($query, true);
    }


    public function countSearchResults(string $search = '')
    {
        $queryBuilder = $this->createQueryBuilder('a');

        if ($search) {
            $queryBuilder->where('a.nomArticle LIKE :search')
                         ->setParameter('search', '%' . $search . '%');
        }

        return (int) $queryBuilder->select('COUNT(a.id)')
                                  ->getQuery()
                                  ->getSingleScalarResult();
    }
}
