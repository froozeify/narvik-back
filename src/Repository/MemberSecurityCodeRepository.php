<?php

namespace App\Repository;

use App\Entity\Member;
use App\Entity\MemberSecurityCode;
use App\Enum\MemberSecurityCodeTrigger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MemberSecurityCode>
 */
class MemberSecurityCodeRepository extends ServiceEntityRepository {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, MemberSecurityCode::class);
  }

  public function findLastOneForMember(Member $member, MemberSecurityCodeTrigger $trigger): ?MemberSecurityCode {
    return $this->createQueryBuilder('m')
                ->andWhere('m.member = :member')
                ->andWhere('m.trigger = :trigger')
                ->andWhere(':expire_at <= m.expireAt')
                ->setParameter('member', $member)
                ->setParameter('trigger', $trigger)
                ->setParameter('expire_at', new \DateTimeImmutable())
                ->orderBy('m.createdAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
  }

  public function findAllByTrigger(Member $member, MemberSecurityCodeTrigger $trigger): array {
    return $this->createQueryBuilder('m')
                ->andWhere('m.member = :member')
                ->andWhere('m.trigger = :trigger')
                ->setParameter('member', $member)
                ->setParameter('trigger', $trigger)
                ->getQuery()
                ->getResult();
  }

  /**
   * Return codes that have expired and can be removed.
   *
   * @return MemberSecurityCode[]
   */
  public function findExpired(): array {
    return $this->createQueryBuilder('m')
                ->andWhere('m.expireAt <= :expire_at')
                ->setParameter('expire_at', new \DateTimeImmutable('-1 days'))
                ->getQuery()
                ->getResult();
  }
}
