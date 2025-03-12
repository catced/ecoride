<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class Search_ResultsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    // public function findByVoyagesPasses(User $user, \DateTime $dateActuelle)
    // {
    //     return $this->createQueryBuilder('v')
    //         ->innerJoin('v.passagers', 'p')
    //         ->where('p = :user')
    //         ->andWhere('v.date < :dateActuelle')
    //         ->setParameter('user', $user)
    //         ->setParameter('dateActuelle', $dateActuelle)
    //         ->orderBy('v.date', 'DESC')
    //         ->getQuery()
    //         ->getResult();
    // }

    // public function findByVoyagesAVenir(User $user, \DateTime $dateActuelle)
    // {
    //     return $this->createQueryBuilder('v')
    //         ->innerJoin('v.passagers', 'p')
    //         ->where('p = :user')
    //         ->andWhere('v.date >= :dateActuelle')
    //         ->setParameter('user', $user)
    //         ->setParameter('dateActuelle', $dateActuelle)
    //         ->orderBy('v.date', 'ASC')
    //         ->getQuery()
    //         ->getResult();
    // }

//    /**
//     * @return Booking[] Returns an array of Booking objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Booking
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
