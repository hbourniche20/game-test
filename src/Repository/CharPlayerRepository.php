<?php

namespace App\Repository;

use App\Entity\CharPlayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CharPlayer|null find($id, $lockMode = null, $lockVersion = null)
 * @method CharPlayer|null findOneBy(array $criteria, array $orderBy = null)
 * @method CharPlayer[]    findAll()
 * @method CharPlayer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CharPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CharPlayer::class);
    }

    // /**
    //  * @return CharPlayer[] Returns an array of CharPlayer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CharPlayer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
