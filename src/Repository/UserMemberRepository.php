<?php

namespace App\Repository;

use App\Entity\UserMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserMember>
 */
class UserMemberRepository extends ServiceEntityRepository {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, UserMember::class);
  }

  //    /**
  //     * @return UserClub[] Returns an array of UserClub objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('u')
  //            ->andWhere('u.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('u.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?UserClub
  //    {
  //        return $this->createQueryBuilder('u')
  //            ->andWhere('u.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
