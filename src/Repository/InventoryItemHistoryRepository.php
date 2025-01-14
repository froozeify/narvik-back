<?php

namespace App\Repository;

use App\Entity\ClubDependent\Plugin\Sale\InventoryItemHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InventoryItemHistory>
 */
class InventoryItemHistoryRepository extends ServiceEntityRepository {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, InventoryItemHistory::class);
  }

  //    /**
  //     * @return InventoryItemHistory[] Returns an array of InventoryItemHistory objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('i')
  //            ->andWhere('i.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('i.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?InventoryItemHistory
  //    {
  //        return $this->createQueryBuilder('i')
  //            ->andWhere('i.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
