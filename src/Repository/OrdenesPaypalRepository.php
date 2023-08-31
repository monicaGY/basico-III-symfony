<?php

namespace App\Repository;

use App\Entity\OrdenesPaypal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrdenesPaypal>
 *
 * @method OrdenesPaypal|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrdenesPaypal|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrdenesPaypal[]    findAll()
 * @method OrdenesPaypal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdenesPaypalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrdenesPaypal::class);
    }

//    /**
//     * @return OrdenesPaypal[] Returns an array of OrdenesPaypal objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OrdenesPaypal
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
