<?php

namespace App\Repository;

use App\Entity\Don;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    public function calculerContribution(User $organisateur): void
{
    // Récupérer tous les événements créés par l'organisateur
    $evenements = $organisateur->getEvenements();

    // Initialiser le montant total à payer
    $montantTotal = 0;

    // Parcourir chaque événement
    foreach ($evenements as $evenement) {
        // Calculer le montant pour cet événement : prix * nombre de réservations
        $montantTotal += $evenement->getPrix() * $evenement->getNbrPlaceReserver();
    }
    $totalDons = 0;
    foreach ($organisateur->getDons() as $don) {
        if ($don->getType() === "contribution"&&$don->getStatus()=="confirme") {
            $totalDons += $don->getMontant();
        }
    }
    $montantTotal=$montantTotal-$totalDons;
    // Mettre à jour les attributs de l'organisateur
    if($montantTotal>0){
        $organisateur->setMontantAPayer($montantTotal);
    }else{
        $organisateur->setMontantAPayer(0);
    }
    // Enregistrer les modifications
        $entityManager = $this->getEntityManager();
        $entityManager->persist($organisateur);
        $entityManager->flush();
}

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
