<?php

namespace App\Repository;

use App\Entity\Composition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Composition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Composition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Composition[]    findAll()
 * @method Composition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Composition::class);
    }

    // /**
    //  * @return Composition[] Returns an array of Composition objects
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
    public function findOneBySomeField($value): ?Composition
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
