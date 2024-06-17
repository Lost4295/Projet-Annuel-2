<?php

namespace App\Repository;

use App\Entity\Appartement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appartement>
 *
 * @method Appartement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appartement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appartement[]    findAll()
 * @method Appartement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppartementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appartement::class);
    }

    //    /**
    //     * @return Appartement[] Returns an array of Appartement objects
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

    public function findAppart ( string $dest= null, string $startdate= null, string $enddate= null, string $adults= null, string $children= null, string $babies= null)
    {
        $qb = $this->createQueryBuilder('a');
        if (isset($dest) && $dest !== '') {
            $qb
                ->where('a.city LIKE :dest')
                ->setParameter('dest', '%' . $dest . '%')
                ->orWhere('a.country LIKE :dest2')
                ->setParameter('dest2', '%' . $dest . '%');
        }
        if (isset($adults) && $adults !== '') {
            if (isset($children) && $children !== '') {
                if (isset($babies) && $babies !== '') {
                    $voyageurs = $adults + $children + $babies;
                } else {
                    $voyageurs = $adults + $children;
                }
            } else {
                $voyageurs = $adults;
            }
            $qb
                ->andWhere('a.nbVoyageurs >= :adults')
                ->setParameter('adults', $voyageurs);
        }
        if (isset($startdate) && $startdate !== '') {
            $qb
                ->leftJoin('a.locations', 'l')
                ->andWhere('l.dateDebut NOT BETWEEN :startdate AND :enddate')
                ->setParameter('startdate', $startdate)
                ->setParameter('enddate', $enddate)
                ->andWhere('l.dateFin NOT BETWEEN :startdate AND :enddate')
                ->setParameter('startdate', $startdate)
                ->setParameter('enddate', $enddate);
        }
        return $qb->getQuery()->getResult();
    }

    public function getApparts (int $pageSize, int $pageNumber)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->setFirstResult($pageSize * ($pageNumber - 1))
            ->setMaxResults($pageSize);
        return $qb->getQuery()->getResult();
    }
    //    public function findOneBySomeField($value): ?Appartement
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
