<?php

namespace App\Repository;

use App\Entity\RaidGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RaidGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method RaidGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method RaidGame[]    findAll()
 * @method RaidGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RaidGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RaidGame::class);
    }

    // /**
    //  * @return RaidGame[] Returns an array of RaidGame objects
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
    public function findOneBySomeField($value): ?RaidGame
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
