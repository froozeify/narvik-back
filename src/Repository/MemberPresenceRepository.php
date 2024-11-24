<?php

namespace App\Repository;

use App\Entity\ClubDependent\Activity;
use App\Entity\ClubDependent\Member;
use App\Entity\ClubDependent\MemberPresence;
use App\Repository\Interface\PresenceRepositoryInterface;
use App\Repository\Trait\PresenceRepositoryTrait;
use App\Service\GlobalSettingService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MemberPresence>
 *
 * @method MemberPresence|null find($id, $lockMode = null, $lockVersion = null)
 * @method MemberPresence|null findOneBy(array $criteria, array $orderBy = null)
 * @method MemberPresence[]    findAll()
 * @method MemberPresence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberPresenceRepository extends ServiceEntityRepository implements PresenceRepositoryInterface {
  use PresenceRepositoryTrait;

  private GlobalSettingService $globalSettingService;

  public function __construct(ManagerRegistry $registry, GlobalSettingService $globalSettingService) {
    parent::__construct($registry, MemberPresence::class);
    $this->globalSettingService = $globalSettingService;
  }

  public function findOneToday(Member $member): ?MemberPresence {
    return $this->findOneByDay($member, new \DateTime());
  }

  public function findOneByDay(Member $member, \DateTime $date): ?MemberPresence {
    $qb = $this->createQueryBuilder('m');
    return $this->applyDayConstraint($qb, $date)
      ->andWhere("m.member = :member")
      ->setParameter("member", $member)
      ->getQuery()->getOneOrNullResult();
  }

  public function findLastOneByActivity(Member $member, Activity $activity): ?MemberPresence {
    $qb = $this->createQueryBuilder('m');
    return $qb
      ->andWhere("m.member = :member")
      ->innerJoin("m.activities", "a", Join::WITH, $qb->expr()->eq("a.id", ":activity"))
      ->orderBy("m.date", "DESC")
      ->setParameter("activity", $activity)
      ->setParameter("member", $member)
      ->setMaxResults(1)
      ->getQuery()->getOneOrNullResult();
  }

}
