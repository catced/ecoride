<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class, User::class);
    }

    public function countBookingsByDay()
    {
        return $this->createQueryBuilder('b')
            ->select('b.createdAt as day, SUM(b.seatsBooked) as booking_count')
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->getQuery()
            ->getResult();
    // }
    // $qb = $this->createQueryBuilder('r')
    // ->select('r.createdAt as date, COUNT(r.id) as count')
    // ->groupBy('r.createdAt')
    // ->getQuery();

    // $results = $qb->getResult();

    // Retourner les r�sultats format�s pour plus de flexibilit�
    $formattedResults = [];
    foreach ($results as $result) {
        $date = $result['date']->format('d-m-Y');
        $formattedResults[] = [
            'date' => $date,
            'count' => $result['count']
        ];
    }

    return $formattedResults;
    }

    // public function findByVoyagesPasses(User $user, \DateTime $dateActuelle)
    // {
    //     return $this->createQueryBuilder('b')
    //         ->innerJoin('b.ride', 'r')
    //         ->where('r = :user')
    //         // ->andWhere('b.createdat < :dateActuelle')
    //         ->setParameter('user', $user)
    //         // ->setParameter('dateActuelle', $dateActuelle)
    //         // ->orderBy('b.createdat', 'DESC')
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
