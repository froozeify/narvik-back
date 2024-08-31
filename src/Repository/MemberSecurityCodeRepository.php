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

  public function findOneBySecurityCode(Member $member, MemberSecurityCodeTrigger $trigger, string $securityCode): ?MemberSecurityCode {
    return $this->createQueryBuilder('m')
                ->andWhere('m.member = :member')
                ->andWhere('m.trigger = :trigger')
                ->andWhere('m.code = :code')
                ->andWhere('m.expireAt <= :expire_at')
                ->setParameter('member', $member)
                ->setParameter('trigger', $trigger)
                ->setParameter('code', $securityCode)
                ->setParameter('expire_at', new \DateTimeImmutable())
                ->getQuery()
                ->getOneOrNullResult();
  }

  /**
   * Return codes that have expired more than one day ago
   * So they can be safely removed
   *
   * @return array
   */
  public function findExpired(): array {
    return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField > :expire_at')
            ->setParameter('expire_at', new \DateTimeImmutable('+1 days'))
            ->getQuery()
            ->getResult();
  }
}
