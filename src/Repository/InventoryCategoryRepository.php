<?php

namespace App\Repository;

use App\Entity\ClubDependent\InventoryCategory;
use App\Repository\Interface\SortableRepositoryInterface;
use App\Repository\Trait\SortableEntityRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InventoryCategory>
 *
 * @method InventoryCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method InventoryCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method InventoryCategory[]    findAll()
 * @method InventoryCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InventoryCategoryRepository extends ServiceEntityRepository implements SortableRepositoryInterface {
  use SortableEntityRepositoryTrait;

  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, InventoryCategory::class);
  }

  //    /**
  //     * @return InventoryCategory[] Returns an array of InventoryCategory objects
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

  //    public function findOneBySomeField($value): ?InventoryCategory
  //    {
  //        return $this->createQueryBuilder('i')
  //            ->andWhere('i.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
