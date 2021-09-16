<?php

namespace App\Repository;

use App\Entity\RaidQuest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RaidQuest|null find($id, $lockMode = null, $lockVersion = null)
 * @method RaidQuest|null findOneBy(array $criteria, array $orderBy = null)
 * @method RaidQuest[]    findAll()
 * @method RaidQuest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RaidQuestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RaidQuest::class);
    }

    // /**
    //  * @return RaidQuest[] Returns an array of RaidQuest objects
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
    public function findOneBySomeField($value): ?RaidQuest
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
