<?php

namespace App\Repository;

use App\Entity\WinCredit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WinCredit>
 */
class WinCreditRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WinCredit::class);
        
    }

    //    /**
    //     * @return WinCredit[] Returns an array of WinCredit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?WinCredit
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function getTotalCredits(): int
    {
        $result = $this->createQueryBuilder('w')
        ->select(
            'SUM(w.Monday) as Monday',
            'SUM(w.Tuesday) as Tuesday',
            'SUM(w.Wednesday) as Wednesday',
            'SUM(w.Thursday) as Thursday',
            'SUM(w.Friday) as Friday',
            'SUM(w.Saturday) as Saturday',
            'SUM(w.Sunday) as Sunday'
        )
        ->getQuery()
        ->getSingleResult();
        return array_sum($result); 
        // return (int) $this->createQueryBuilder('w')
        //     ->select('SUM(w.Monday + w.Tuesday + w.Wednesday + w.Thursday + w.Friday + w.Saturday + w.Sunday) as total')
        //     ->getQuery()
        //     ->getSingleScalarResult();
    }

    public function getCreditsByDay(): array
    {
        return $this->createQueryBuilder('w')
            ->select('SUM(w.Monday) as Monday, SUM(w.Tuesday) as Tuesday, SUM(w.Wednesday) as Wednesday, SUM(w.Thursday) as Thursday, SUM(w.Friday) as Friday, SUM(w.Saturday) as Saturday, SUM(w.Sunday) as Sunday')
            ->getQuery()
            ->getSingleResult();
    }
}
