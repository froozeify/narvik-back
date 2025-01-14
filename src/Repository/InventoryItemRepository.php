<?php

namespace App\Repository;

use App\Entity\ClubDependent\Plugin\Sale\InventoryItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InventoryItem>
 *
 * @method InventoryItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method InventoryItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method InventoryItem[]    findAll()
 * @method InventoryItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InventoryItemRepository extends ServiceEntityRepository {

  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, InventoryItem::class);
  }

  //    /**
  //     * @return InventoryItem[] Returns an array of InventoryItem objects
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

  //    public function findOneBySomeField($value): ?InventoryItem
  //    {
  //        return $this->createQueryBuilder('i')
  //            ->andWhere('i.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
