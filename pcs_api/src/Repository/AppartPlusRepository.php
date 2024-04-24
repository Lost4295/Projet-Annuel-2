<?php

namespace App\Repository;

use App\Entity\AppartPlus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AppartPlus>
 *
 * @method AppartPlus|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppartPlus|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppartPlus[]    findAll()
 * @method AppartPlus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppartPlusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppartPlus::class);
    }

    //    /**
    //     * @return AppartPlus[] Returns an array of AppartPlus objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?AppartPlus
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
