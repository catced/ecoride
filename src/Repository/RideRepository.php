<?php

namespace App\Repository;

use App\Entity\Ride;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository
 */
class RideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ride::class);
    }
    
    // Compter les trajets par jour
    public function countRidesByDay()
    {
        return $this->createQueryBuilder('r')
            ->select('r.departureDay as day, COUNT(r.id) as ride_count')
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->getQuery()
            ->getResult();
    
    // $qb = $this->createQueryBuilder('r')
    // ->select('r.departureDay as date, COUNT(r.id) as count')
    // ->groupBy('r.departureDay')
    // ->getQuery();

    // $results = $qb->getResult();
    

    // Retourner les résultats formatés pour plus de flexibilité
        $formattedResults = [];
        foreach ($results as $result) {
            $date = $result['date'];
            $formattedResults[] = [
                'date' => $date,
                'count' => $result['count']
            ];
        }

    return $formattedResults;
    }

    //    /**
    //     * @return Toto[] Returns an array of Toto objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Toto
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
//     public function findBySearchQuery(string $query)
// {
//     return $this->createQueryBuilder('r')
//         ->where('r.departure LIKE :query OR r.destination LIKE :query')
//         ->setParameter('query', '%' . $query . '%')
//         ->getQuery()
//         ->getResult();
// }

    public function findWithVehicle(int $id): ?Ride
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.vehicle', 'v')
            ->addSelect('v')
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // public function getRidesByReview()
    // {
    //     return $this->createQueryBuilder('r')
    //         ->leftJoin('r.reviews', 'rev')
    //         ->leftJoin('r.passenger', 'p')
    //         ->leftJoin('r.driver', 'd')
    //         ->addSelect('rev', 'p', 'd')
    //         ->where('(rev.rating >= 4 AND rev.validated = false AND rev.comment IS NOT NULL)')
    //         ->orWhere('rev.rating <= 3')
    //         ->orderBy('r.departureTime', 'ASC')
    //         ->getQuery()
    //         ->getResult();
    // }

}
