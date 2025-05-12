<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Trouve les événements par titre.
     */
    public function findByTitle(string $query): array
    {
        return $this->createQueryBuilder('e')
            ->where('LOWER(e.titre) LIKE LOWER(:query)')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('e.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les événements par catégorie.
     */
    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.categorie = :category')
            ->setParameter('category', $category)
            ->orderBy('e.date_e', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les événements à venir.
     */
    public function findUpcomingEvents(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.date_e >= :currentDate')
            ->setParameter('currentDate', new \DateTime())
            ->orderBy('e.date_e', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les événements par recherche dans le titre ou la description avec pagination.
     */
    public function findBySearchQuery(string $query, int $page = 1, int $limit = 10): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->where('LOWER(e.titre) LIKE LOWER(:query) OR LOWER(e.description) LIKE LOWER(:query)')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('e.date_e', 'ASC')
            ->getQuery();

        $paginator = new Paginator($queryBuilder);
        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $paginator;
    }

    /**
     * Trouve les événements paginés.
     */
    public function findPaginated(int $page = 1, int $limit = 10): Paginator
    {
        $query = $this->createQueryBuilder('e')
            ->orderBy('e.date_e', 'ASC')
            ->getQuery();

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $paginator;
    }

    /**
     * Compte le nombre total d'événements.
     */
    public function countAll(): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.idE)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve les événements par localisation (latitude et longitude).
     */
    public function findByLocation(float $latitude, float $longitude, float $radius = 10): array
    {
        $earthRadius = 6371; // Rayon de la Terre en km

        return $this->createQueryBuilder('e')
            ->addSelect(
                "( $earthRadius * acos(cos(radians(:latitude)) * cos(radians(e.latitude)) * cos(radians(e.longitude) - radians(:longitude)) + sin(radians(:latitude)) * sin(radians(e.latitude)) ) AS distance"
            )
            ->setParameter('latitude', $latitude)
            ->setParameter('longitude', $longitude)
            ->having('distance <= :radius')
            ->setParameter('radius', $radius)
            ->orderBy('distance', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne le nombre d'événements par catégorie.
     *
     * @return array
     */
    public function countEventsByCategory(): array
    {
        return $this->createQueryBuilder('e')
            ->select('e.categorie as category, COUNT(e.id_e) as count')
            ->groupBy('e.categorie')
            ->getQuery()
            ->getResult();
    }
}