<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VoyageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function findByVoyagesPasses(User $user, \DateTime $dateActuelle)
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.user', 'p')
            ->where('p = :user')
            ->andWhere('b.createdAt < :dateActuelle')
            ->setParameter('user', $user)
            ->setParameter('dateActuelle', $dateActuelle)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByVoyagesAVenir(User $user, \DateTime $dateActuelle)
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.user', 'p')
            ->where('p = :user')
            ->andWhere('b.createdAt >= :dateActuelle')
            ->setParameter('user', $user)
            ->setParameter('dateActuelle', $dateActuelle)
            ->orderBy('b.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
