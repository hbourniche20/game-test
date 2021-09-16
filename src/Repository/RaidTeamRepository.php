<?php

namespace App\Repository;

use App\Entity\RaidTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RaidTeam|null find($id, $lockMode = null, $lockVersion = null)
 * @method RaidTeam|null findOneBy(array $criteria, array $orderBy = null)
 * @method RaidTeam[]    findAll()
 * @method RaidTeam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RaidTeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RaidTeam::class);
    }

    // /**
    //  * @return RaidTeam[] Returns an array of RaidTeam objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RaidTeam
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
