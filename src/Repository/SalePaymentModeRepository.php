<?php

namespace App\Repository;

use App\Entity\SalePaymentMode;
use App\Repository\Interface\SortableRepositoryInterface;
use App\Repository\Trait\SortableEntityRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SalePaymentMode>
 */
class SalePaymentModeRepository extends ServiceEntityRepository implements SortableRepositoryInterface {
  use SortableEntityRepositoryTrait;

  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, SalePaymentMode::class);
  }

  //    /**
  //     * @return SalePaymentMode[] Returns an array of SalePaymentMode objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('s')
  //            ->andWhere('s.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('s.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?SalePaymentMode
  //    {
  //        return $this->createQueryBuilder('s')
  //            ->andWhere('s.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
