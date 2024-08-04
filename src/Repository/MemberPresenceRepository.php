<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Member;
use App\Entity\MemberPresence;
use App\Enum\GlobalSetting;
use App\Service\GlobalSettingService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MemberPresence>
 *
 * @method MemberPresence|null find($id, $lockMode = null, $lockVersion = null)
 * @method MemberPresence|null findOneBy(array $criteria, array $orderBy = null)
 * @method MemberPresence[]    findAll()
 * @method MemberPresence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberPresenceRepository extends ServiceEntityRepository {
  private GlobalSettingService $globalSettingService;

  public function __construct(ManagerRegistry $registry, GlobalSettingService $globalSettingService) {
    parent::__construct($registry, MemberPresence::class);
    $this->globalSettingService = $globalSettingService;
  }

  private function applyTodayConstraint(QueryBuilder $qb): QueryBuilder {
    return $this->applyDayConstraint($qb, new \DateTime());
  }

  private function applyDayConstraint(QueryBuilder $qb, \DateTime $date): QueryBuilder {
    return $qb->andWhere(
      $qb->expr()->between('m.date', ':from', ':to'),
    )
       ->setParameter('from', $date->setTime(0, 0, 0))
       ->setParameter('to', $date->setTime(23, 59, 59))
    ;
  }

  private function applyActivityExclusionConstraint(QueryBuilder $qb): QueryBuilder {
    $ignoredActivities = $this->globalSettingService->getSettingValue(GlobalSetting::IGNORED_ACTIVITIES_OPENING_STATS);
    if ($ignoredActivities) {
      $qb->leftJoin('m.activities', 'mpa')
         ->andWhere($qb->expr()->notIn("mpa.id", ":ids"))
         ->setParameter("ids", array_values(json_decode($ignoredActivities, true)));
    }
    return $qb;
  }

  /**
   * @return MemberPresence[] Returns an array of MemberPresence objects
   */
  public function findAllPresentToday() {
    $qb = $this->createQueryBuilder('m');
    return $this->applyTodayConstraint($qb)
      ->orderBy('m.createdAt', 'DESC')
      ->getQuery()->getResult();
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

  public function findAllByActivity(Activity $activity): ?array {
    $qb = $this->createQueryBuilder('m');
    return $qb
      ->innerJoin("m.activities", "a", Join::WITH, $qb->expr()->eq("a.id", ":activity"))
      ->orderBy("m.date", "DESC")
      ->setParameter("activity", $activity)
      ->getQuery()->getResult();
  }

  /**********************************************************
   *                        METRICS
   *********************************************************/

  public function countTotalMembersPresencesYearlyUntilDate(\DateTime $maxDate): int {
    $startYear = (new \DateTime())
      ->setDate((int) $maxDate->format("Y"), 1, 1)
      ->setTime(0, 0, 0);

    $qb = $this->createQueryBuilder("m");
    return $qb
      ->select($qb->expr()->count("m.id"))
      ->andWhere($qb->expr()->between("m.date", ":from", ":to"))
      ->setParameter("from", $startYear)
      ->setParameter("to", $maxDate)
      ->getQuery()->getSingleScalarResult();
  }

  public function countNumberOfMemberPresenceDaysYearlyUntilDate(\DateTime $maxDate): int {
    $startYear = (new \DateTime())
      ->setDate((int) $maxDate->format("Y"), 1, 1)
      ->setTime(0, 0, 0);

    $qb = $this->createQueryBuilder("m");
    $this->applyActivityExclusionConstraint($qb);

    return $qb
      ->select($qb->expr()->countDistinct("m.date"))
      ->andWhere($qb->expr()->between("m.date", ":from", ":to"))
      ->setParameter("from", $startYear)
      ->setParameter("to", $maxDate)
      ->getQuery()->getSingleScalarResult();
  }

  public function countTotalMembersPresencesYearlyUntilToday(): int {
    return $this->countTotalMembersPresencesYearlyUntilDate(new \DateTime());
  }

  public function countTotalMembersPresencesYearlyForPreviousYear(): int {
    $lastYear = new \DateTime();
    $lastYear->setDate((int) $lastYear->format("Y") - 1, $lastYear->format("m"), $lastYear->format("d"));

    return $this->countTotalMembersPresencesYearlyUntilDate($lastYear);
  }

  public function countTotalMembersPresences(): int {
    $qb = $this->createQueryBuilder("m");
    return $qb
      ->select($qb->expr()->count("m.id"))
      ->getQuery()->getSingleScalarResult();
  }

  public function countNumberOfMemberPresenceDaysYearlyUntilToday(): int {
    return $this->countNumberOfMemberPresenceDaysYearlyUntilDate(new \DateTime());
  }

  public function countNumberOfMemberPresenceDaysYearlyForPreviousYear(): int {
    $lastYear = new \DateTime();
    $lastYear->setDate((int) $lastYear->format("Y") - 1, $lastYear->format("m"), $lastYear->format("d"));

    return $this->countNumberOfMemberPresenceDaysYearlyUntilDate($lastYear);
  }

  public function countPresencesPerActivitiesYearlyUntilDate(\DateTime $maxDate) {
    $startYear = (new \DateTime())
      ->setDate((int) $maxDate->format("Y"), 1, 1)
      ->setTime(0, 0, 0);

    $qb = $this->createQueryBuilder("m");
    return $qb
      ->select("a.name")
      ->addSelect($qb->expr()->count("a.name") . ' AS total')
      ->innerJoin("m.activities", "a")
      ->groupBy("a.name")
      ->orderBy("a.name")

      ->andWhere($qb->expr()->between("m.date", ":from", ":to"))
      ->setParameter("from", $startYear)
      ->setParameter("to", $maxDate)
      ->getQuery()->getResult();
  }

  public function countPresencesPerActivitiesYearlyUntilToday() {
    return $this->countPresencesPerActivitiesYearlyUntilDate(new \DateTime());
  }

  public function countPresencesPerActivitiesYearlyForPreviousYear() {
    $lastYear = new \DateTime();
    $lastYear->setDate((int) $lastYear->format("Y") - 1, $lastYear->format("m"), $lastYear->format("d"));
    return $this->countPresencesPerActivitiesYearlyUntilDate($lastYear);
  }

}
