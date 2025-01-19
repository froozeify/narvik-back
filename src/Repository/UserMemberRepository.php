<?php

namespace App\Repository;

use App\Entity\ClubDependent\Member;
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

  public function findOneByMember(Member $member): ?UserMember {
    $query = $this->createQueryBuilder('u')
      ->andWhere('u.member = :member')
      ->setParameter('member', $member)
      ->setMaxResults(1)
      ->getQuery();

    try {
      return $query->getOneOrNullResult();
    } catch (\Exception) {
      return null;
    }
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
}
