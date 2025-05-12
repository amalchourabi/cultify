<?php

namespace App\Repository;

use App\Entity\ContenuMultiMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContenuMultiMedia>
 */
class ContenuMultiMediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContenuMultiMedia::class);
    }
    public function findByTitle(string $title): array
{
    return $this->createQueryBuilder('c')
        ->where('c.TitreMedia LIKE :title')
        ->setParameter('title', '%' . $title . '%')
        ->getQuery()
        ->getResult();
}
// src/Repository/ContenuMultiMediaRepository.php


    public function countContenusByCategory(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.CategorieMedia as category, COUNT(c.idContenu) as count')
            ->groupBy('c.CategorieMedia')
            ->getQuery()
            ->getResult();
    }

}
    //    /**
    //     * @return ContenuMultiMedia[] Returns an array of ContenuMultiMedia objects
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

    //    public function findOneBySomeField($value): ?ContenuMultiMedia
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

