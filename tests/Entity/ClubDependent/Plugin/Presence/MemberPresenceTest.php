<?php

namespace App\Tests\Entity\ClubDependent\Plugin\Presence;

use App\Entity\ClubDependent\Plugin\Presence\MemberPresence;
use App\Enum\ClubRole;
use App\Tests\Entity\Abstract\AbstractEntityClubLinkedTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\ActivityFactory;
use App\Tests\Factory\MemberPresenceFactory;
use App\Tests\Story\_InitStory;
use App\Tests\Story\ActivityStory;
use function Zenstruck\Foundry\faker;

class MemberPresenceTest extends AbstractEntityClubLinkedTestCase {
  protected int $TOTAL_SUPER_ADMIN = 10;
  protected int $TOTAL_ADMIN_CLUB_1 = 10;
  protected int $TOTAL_ADMIN_CLUB_2 = 5;
  protected int $TOTAL_SUPERVISOR_CLUB_1 = 10;
  protected int $TOTAL_BADGER_CLUB_1 = 10;
  protected int $TOTAL_BADGER_CLUB_2 = 5;

  protected function getClassname(): string {
    return MemberPresence::class;
  }

  protected function getRootUrl(): string {
    return "/member-presences";
  }

  protected function getCollectionGrantedAccess() : array {
    $access = parent::getCollectionGrantedAccess();
    $access[ClubRole::badger->value] = true;
    return $access;
  }

  public function initDefaultFixtures(): void {
    MemberPresenceFactory::new([
      'date'       => \DateTimeImmutable::createFromMutable(faker()->dateTimeBetween('now', 'now')),
      'member' => _InitStory::MEMBER_member_club_1(),
    ])->many(5)->create();
    MemberPresenceFactory::new([
      'date'       => \DateTimeImmutable::createFromMutable(faker()->dateTimeBetween('-10 days', '-2 days')),
      'member' => _InitStory::MEMBER_member_club_1(),
    ])->many(5)->create();

    MemberPresenceFactory::new([
      'member' => _InitStory::MEMBER_member_club_2(),
      'activities' => [ActivityStory::getRandom('activities_club2')],
    ])->many(5)->create();
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

  public function testGetTodayPresences(): void {
    $this->makeAllLoggedRequests(
      adminClub2Code: ResponseCodeEnum::ok,
      badgerClub1Code: ResponseCodeEnum::ok,
      badgerClub2Code: ResponseCodeEnum::ok,
      requestFunction: function (string $level, ?int $id) {
        $club = "club_" . ($id ?? 1);
        $this->makeGetRequest($this->getRootWClubUrl(_InitStory::$club()) . "/-/today");
      },
    );

    $this->loggedAsBadgerClub1();
    $response = $this->makeGetRequest($this->getRootWClubUrl(_InitStory::club_1()) . "/-/today");
    $this->assertResponseStatusCodeSame(ResponseCodeEnum::ok->value);
    $this->assertCount(5, $response->toArray()['member']);

  }
}
