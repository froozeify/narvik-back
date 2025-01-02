<?php

namespace App\Repository\Interface;

use App\Entity\ClubDependent\Plugin\Presence\Activity;

interface PresenceRepositoryInterface {
  public function findAllPresentToday(): array;
  public function findAllByActivity(Activity $activity): ?array;

  /**********************************************************
   *                        METRICS
   *********************************************************/

  public function countTotalPresencesYearlyUntilDate(\DateTime $maxDate): int;
  public function countNumberOfPresenceDaysYearlyUntilDate(\DateTime $maxDate): int;
  public function countTotalPresencesYearlyUntilToday(): int;
  public function countTotalPresencesYearlyForPreviousYear(): int;
  public function countTotalPresences(): int;
  public function countNumberOfPresenceDaysYearlyUntilToday(): int;
  public function countNumberOfPresenceDaysYearlyForPreviousYear(): int;
  public function countPresencesPerActivitiesYearlyUntilDate(\DateTime $maxDate);
  public function countPresencesPerActivitiesYearlyUntilToday();
  public function countPresencesPerActivitiesYearlyForPreviousYear();

}
