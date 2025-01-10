<?php

namespace App\Repository\Trait;

use App\Entity\Club;
use App\Entity\ClubDependent\Plugin\Presence\Activity;
use App\Enum\GlobalSetting;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

trait PresenceRepositoryTrait {
  use ClubLinkedTrait;

  private function applyTodayConstraint(QueryBuilder $qb): QueryBuilder {
    return $this->applyDayConstraint($qb, new \DateTime());
  }

  private function applyDayConstraint(QueryBuilder $qb, \DateTime $date): QueryBuilder {
    $qb->andWhere($qb->expr()->between('m.date', ':from', ':to'))
       ->setParameter('from', $date->setTime(0, 0, 0))
       ->setParameter('to', $date->setTime(23, 59, 59));
    return $qb;
  }

  private function applyActivityExclusionConstraint(QueryBuilder $qb): void {
    $ignoredActivities = $this->globalSettingService->getSettingValue(GlobalSetting::IGNORED_ACTIVITIES_OPENING_STATS);
    if ($ignoredActivities) {
      $ids = array_values(json_decode($ignoredActivities, true));
      if (empty($ids)) {
        return;
      }

      $qb->leftJoin('m.activities', 'mpa')
         ->andWhere($qb->expr()->notIn("mpa.id", ":ids"))
         ->setParameter("ids", $ids)
      ;
    }
  }

  /**
   * @return array Returns an array of presences
   */
  public function findAllPresentToday(Club $club): array {
    $qb = $this->createQueryBuilder('m');

    return
      $this->applyTodayConstraint($qb)
            ->andWhere($qb->expr()->eq('m.' . $this->getClassName()::getClubSqlPath(), ':club'))
            ->setParameter(':club', $club)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()->getResult()
      ;
  }

  public function findAllByActivity(Activity $activity): ?array {
    $qb = $this->createQueryBuilder('m');
    return
      $qb->innerJoin("m.activities", "a", Join::WITH, $qb->expr()->eq("a.id", ":activity"))
         ->orderBy("m.date", "DESC")
         ->setParameter("activity", $activity)
         ->getQuery()->getResult()
      ;
  }

  /**********************************************************
   *                        METRICS
   *********************************************************/

  public function countTotalPresencesYearlyUntilDate(\DateTime $maxDate): int {
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

  public function countNumberOfPresenceDaysYearlyUntilDate(\DateTime $maxDate): int {
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

  public function countTotalPresencesYearlyUntilToday(): int {
    return $this->countTotalPresencesYearlyUntilDate(new \DateTime());
  }

  public function countTotalPresencesYearlyForPreviousYear(): int {
    $lastYear = new \DateTime();
    $lastYear->setDate((int) $lastYear->format("Y") - 1, $lastYear->format("m"), $lastYear->format("d"));

    return $this->countTotalPresencesYearlyUntilDate($lastYear);
  }

  public function countTotalPresences(): int {
    $qb = $this->createQueryBuilder("m");
    return $qb
      ->select($qb->expr()->count("m.id"))
      ->getQuery()->getSingleScalarResult();
  }

  public function countNumberOfPresenceDaysYearlyUntilToday(): int {
    return $this->countNumberOfPresenceDaysYearlyUntilDate(new \DateTime());
  }

  public function countNumberOfPresenceDaysYearlyForPreviousYear(): int {
    $lastYear = new \DateTime();
    $lastYear->setDate((int) $lastYear->format("Y") - 1, $lastYear->format("m"), $lastYear->format("d"));

    return $this->countNumberOfPresenceDaysYearlyUntilDate($lastYear);
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
