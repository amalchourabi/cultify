<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Get full user details with related history, offers, categories, and reclamations.
     */
    public function findUserWithRelations(int $userId): ?User
    {
        $entityManager = $this->getEntityManager();

        $dql = "
            SELECT u, h, c, o, r
            FROM App\Entity\User u
            LEFT JOIN u.history h
            LEFT JOIN u.contributedCategories c
            LEFT JOIN u.offers o
            LEFT JOIN u.reclamations r
            WHERE u.id = :id
        ";

        return $entityManager->createQuery($dql)
            ->setParameter('id', $userId)
            ->getOneOrNullResult();
    }
}
