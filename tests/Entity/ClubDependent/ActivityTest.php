<?php

namespace App\Tests\Entity\ClubDependent;

use App\Entity\ClubDependent\Activity;
use App\Enum\ClubRole;
use App\Tests\Entity\Abstract\AbstractEntityClubLinkedTestCase;
use App\Tests\Story\ActivityStory;

class ActivityTest extends AbstractEntityClubLinkedTestCase {
  protected int $TOTAL_SUPER_ADMIN = 9;
  protected int $TOTAL_ADMIN_CLUB_1 = 9;
  protected int $TOTAL_ADMIN_CLUB_2 = 1;
  protected int $TOTAL_SUPERVISOR_CLUB_1 = 9;
  protected int $TOTAL_MEMBER_CLUB_1 = 9;

  protected function getClassname(): string {
    return Activity::class;
  }

  protected function getRootUrl(): string {
    return "/activities";
  }

  protected function getCollectionGrantedAccess() : array {
    $access = parent::getCollectionGrantedAccess();
    $access[ClubRole::member->value] = true;
    $access[ClubRole::badger->value] = true;
    return $access;
  }

  public function initDefaultFixtures(): void {
    ActivityStory::load();
  }

  public function testCreate(): void {
    self::markTestSkipped();
  }

  public function testPatch(): void {
    self::markTestSkipped();
  }

  public function testDelete(): void {
    self::markTestSkipped();
  }

  // TODO: Add activity merge test

}
