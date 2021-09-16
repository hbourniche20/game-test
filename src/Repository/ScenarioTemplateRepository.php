<?php

namespace App\Repository;

use App\Entity\ScenarioTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ScenarioTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScenarioTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScenarioTemplate[]    findAll()
 * @method ScenarioTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScenarioTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScenarioTemplate::class);
    }

    // /**
    //  * @return ScenarioTemplate[] Returns an array of ScenarioTemplate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ScenarioTemplate
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
