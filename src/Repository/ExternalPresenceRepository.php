<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Member;
use App\Entity\ExternalPresence;
use App\Enum\GlobalSetting;
use App\Service\GlobalSettingService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExternalPresence>
 *
 * @method ExternalPresence|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExternalPresence|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExternalPresence[]    findAll()
 * @method ExternalPresence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExternalPresenceRepository extends ServiceEntityRepository {
  private GlobalSettingService $globalSettingService;

  public function __construct(ManagerRegistry $registry, GlobalSettingService $globalSettingService) {
    parent::__construct($registry, ExternalPresence::class);
    $this->globalSettingService = $globalSettingService;
  }

  private function applyTodayConstraint(QueryBuilder $qb) {
    return $qb->andWhere(
        $qb->expr()->between('m.date', ':from', ':to'),
      )
      ->setParameter('from', (new \DateTime())->setTime(0, 0, 0))
      ->setParameter('to', (new \DateTime())->setTime(23, 59, 59))
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
   * @return ExternalPresence[] Returns an array of ExternalPresence objects
   */
  public function findAllPresentToday() {
    $qb = $this->createQueryBuilder('m');
    return $this->applyTodayConstraint($qb)
      ->orderBy('m.createdAt', 'DESC')
      ->getQuery()->getResult();
  }

  public function findAllByActivity(Activity $activity): ?array {
    $qb = $this->createQueryBuilder('m');
    return $qb
      ->innerJoin("m.activities", "a", Join::WITH, $qb->expr()->eq("a.id", ":activity"))
      ->orderBy("m.date", "DESC")
      ->setParameter("activity", $activity)
      ->getQuery()->getResult();
  }

  public function findAllWithLicence(): ?array {
    $qb = $this->createQueryBuilder('e');
    return $qb
      ->where($qb->expr()->isNotNull("e.licence"))
      ->orderBy("e.licence", "ASC")
      ->getQuery()->getResult();
  }

  /**********************************************************
   *                        METRICS
   *********************************************************/

  public function countTotalExternalPresencesYearlyUntilDate(\DateTime $maxDate): int {
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

  public function countNumberOfExternalPresenceDaysYearlyUntilDate(\DateTime $maxDate): int {
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

  public function countTotalExternalPresencesYearlyUntilToday(): int {
    return $this->countTotalExternalPresencesYearlyUntilDate(new \DateTime());
  }

  public function countTotalExternalPresencesYearlyForPreviousYear(): int {
    $lastYear = new \DateTime();
    $lastYear->setDate((int) $lastYear->format("Y") - 1, $lastYear->format("m"), $lastYear->format("d"));

    return $this->countTotalExternalPresencesYearlyUntilDate($lastYear);
  }

  public function countTotalExternalPresences(): int {
    $qb = $this->createQueryBuilder("m");
    return $qb
      ->select($qb->expr()->count("m.id"))
      ->getQuery()->getSingleScalarResult();
  }

  public function countNumberOfExternalPresenceDaysYearlyUntilToday(): int {
    return $this->countNumberOfExternalPresenceDaysYearlyUntilDate(new \DateTime());
  }

  public function countNumberOfExternalPresenceDaysYearlyForPreviousYear(): int {
    $lastYear = new \DateTime();
    $lastYear->setDate((int) $lastYear->format("Y") - 1, $lastYear->format("m"), $lastYear->format("d"));

    return $this->countNumberOfExternalPresenceDaysYearlyUntilDate($lastYear);
  }

}
