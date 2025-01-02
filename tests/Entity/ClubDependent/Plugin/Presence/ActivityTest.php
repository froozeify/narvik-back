<?php

namespace App\Tests\Entity\ClubDependent\Plugin\Presence;

use App\Entity\ClubDependent\Plugin\Presence\Activity;
use App\Enum\ClubRole;
use App\Tests\Entity\Abstract\AbstractEntityClubLinkedTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\ActivityFactory;
use App\Tests\Story\_InitStory;
use App\Tests\Story\ActivityStory;
use Zenstruck\Foundry\Persistence\Proxy;

class ActivityTest extends AbstractEntityClubLinkedTestCase {
  protected int $TOTAL_SUPER_ADMIN = 9; // 9 = count ActivityStory::activities_club1
  protected int $TOTAL_ADMIN_CLUB_1 = 9;
  protected int $TOTAL_ADMIN_CLUB_2 = 1;
  protected int $TOTAL_SUPERVISOR_CLUB_1 = 9;
  protected int $TOTAL_MEMBER_CLUB_1 = 9;
  protected int $TOTAL_BADGER_CLUB_1 = 9;
  protected int $TOTAL_BADGER_CLUB_2 = 1;

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
    $club1 = _InitStory::club_1();
    $iri = $this->getIriFromResource($club1);

    $payload = [
      "name" => 'Test activity',
    ];

    $payloadCheck = $payload;
    // For the check we update the payload value
    $payloadCheck["club"] = [
      '@id' => $iri,
    ];

    $this->makeAllLoggedRequests(
      $payloadCheck,
      supervisorClub1Code: ResponseCodeEnum::forbidden,
      adminClub1Code: ResponseCodeEnum::created,
      adminClub2Code: ResponseCodeEnum::forbidden,
      superAdminCode: ResponseCodeEnum::created,
      badgerClub2Code: ResponseCodeEnum::forbidden,
      requestFunction: function (string $level, ?int $id) use ($club1, $payload) {
        $this->makePostRequest($this->getRootWClubUrl($club1), $payload);
      },
    );
  }

  public function testCreateToAnotherClub(): void {
    $club1 = _InitStory::club_1();
    $club2 = _InitStory::club_2();
    $iriClub2 = $this->getIriFromResource($club2);

    $payload = [
      "name" => 'Test activity',
      "club" => $iriClub2
    ];

    $this->loggedAsAdminClub1();
    $this->makePostRequest($this->getRootWClubUrl($club1), $payload);
    $this->assertResponseStatusCodeSame(ResponseCodeEnum::bad_request->value);
  }

  public function testPatch(): void {
    $activity = ActivityStory::getRandom("activities_club1");
    $iri = $this->getIriFromResource($activity);

    $payload = [
      "name" => 'Update activity',
    ];

    $this->makeAllLoggedRequests(
      $payload,
      supervisorClub1Code: ResponseCodeEnum::forbidden,
      requestFunction: function (string $level, ?int $id) use ($iri, &$payload) {
        $this->makePatchRequest($iri, $payload);
      },
    );
  }

  public function testDelete(): void {
    $this->makeAllLoggedRequests(
      null,
      supervisorClub1Code: ResponseCodeEnum::forbidden,
      adminClub1Code: ResponseCodeEnum::no_content,
      superAdminCode: ResponseCodeEnum::no_content,
      requestFunction: function (string $level, ?int $id) {
        $activity = ActivityFactory::createOne([
          'name' => 'Test activity to remove',
          'club' => _InitStory::club_1(),
        ]);
        $iri = $this->getIriFromResource($activity);

        $this->makeDeleteRequest($iri);
      },
    );
  }

  public function testActivityMergeWithNotExisting(): void {
    /** @var Proxy $activity */
    $activity = ActivityStory::getRandom("activities_club1");
    $iri = $this->getIriFromResource($activity->_real());
    $activityUUid = $activity->_real()->getUuid();

    $this->loggedAsSuperAdmin();
    $this->makePatchRequest("$iri/merge", ["target" => "{$activityUUid}-notexisting"]);
    $this->assertResponseStatusCodeSame(ResponseCodeEnum::bad_request->value);
    $this::assertJsonContains([
      "detail" => "Target activity not found",
    ]);
  }

  public function testActivityMergeToSelf() {
    /** @var Proxy $activity */
    $activity = ActivityStory::getRandom("activities_club1");
    $iri = $this->getIriFromResource($activity->_real());
    $activityUUid = $activity->_real()->getUuid();

    $this->loggedAsSuperAdmin();
    $this->makePatchRequest("$iri/merge", ["target" => $activityUUid]);
    $this->assertResponseStatusCodeSame(ResponseCodeEnum::bad_request->value);
    $this::assertJsonContains([
      "detail" => "Can't migrate to self activity",
    ]);
  }

  public function testActivityMergeWithOtherClub(): void {
    /** @var Proxy $activity */
    $activity = ActivityStory::getRandom("activities_club1");
    $iri = $this->getIriFromResource($activity->_real());
    $activityUUid = $activity->_real()->getUuid();
    /** @var Proxy $activityOtherClub */
    $activityOtherClub = ActivityStory::getRandom("activities_club2");
    $activityUuidOtherClub = $activityOtherClub->_real()->getUuid();

    $this->loggedAsSuperAdmin();
    $this->makePatchRequest("$iri/merge", ["target" => $activityUuidOtherClub]);
    $this->assertResponseStatusCodeSame(ResponseCodeEnum::bad_request->value);
    $this::assertJsonContains([
      "detail" => "Activity club does not match target activity club",
    ]);
  }

  public function testActivityMerge(): void {
    $this->makeAllLoggedRequests(
      null,
      supervisorClub1Code: ResponseCodeEnum::forbidden,
      requestFunction: function () {
        $activity = ActivityFactory::createOne([
          'name' => 'activity to receive',
          'club' => _InitStory::club_1(),
        ]);
        $activity2 = ActivityFactory::createOne([
          'name' => 'activity to migrate',
          'club' => _InitStory::club_1(),
        ]);
        $iri = $this->getIriFromResource($activity);
        $this->makePatchRequest("$iri/merge", ["target" => $activity2->getUuid()]);
      },
    );
  }
}
