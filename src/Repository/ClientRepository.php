<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

//    /**
//     * @return Client[] Returns an array of Client objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Client
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function findClientsPaginated(int $page = 1, int $limit = 10)
{
    $queryBuilder = $this->createQueryBuilder('c')
        ->orderBy('c.nom', 'ASC');

    $query = $queryBuilder->getQuery()
        ->setFirstResult(($page - 1) * $limit) 
        ->setMaxResults($limit); 

    return new Paginator($query, true); 
}


public function findByPhone(string $phone, int $page = 1, int $limit = 10)
{
    $queryBuilder = $this->createQueryBuilder('c')
        ->where('c.telephone LIKE :phone')
        ->setParameter('phone', '%' . $phone . '%')
        ->orderBy('c.nom', 'ASC')
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit);

    $query = $queryBuilder->getQuery();

    return new Paginator($query, true);
}

}
