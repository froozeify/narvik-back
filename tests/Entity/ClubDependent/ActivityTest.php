<?php

namespace App\Tests\Entity\ClubDependent;

use App\Entity\ClubDependent\Activity;
use App\Enum\ClubRole;
use App\Tests\Entity\Abstract\AbstractEntityClubLinkedTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\ActivityFactory;
use App\Tests\Story\ActivityStory;
use App\Tests\Story\InitStory;

class ActivityTest extends AbstractEntityClubLinkedTestCase {
  protected int $TOTAL_SUPER_ADMIN = 9; // 9 = count ActivityStory::activities_club1
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
    $club1 = InitStory::club_1();
    $iri = $this->getIriFromResource($club1);

    $payload = [
      "name" => 'Test activity',
      "club" => $iri,
    ];

    $payloadCheck = $payload;
    // For the check we update the payload value
    $payloadCheck["club"] = [
      '@id' => $payload["club"],
    ];

    $this->makeAllLoggedRequests(
      $payloadCheck,
      supervisorClub1Code: ResponseCodeEnum::forbidden,
      adminClub1Code: ResponseCodeEnum::created,
      adminClub2Code: ResponseCodeEnum::bad_request,
      superAdminCode: ResponseCodeEnum::created,
      requestFunction: function (string $level, ?int $id) use ($payload) {
        $this->makePostRequest($this->getRootUrl(), $payload);
      }
    );
  }

  public function testPatch(): void {
    $activity = ActivityStory::getRandom("activities_club1");
    $iri = $this->getIriFromResource($activity);

    $payload = [
      "name" => 'Update activity'
    ];

    $this->makeAllLoggedRequests(
      $payload,
      supervisorClub1Code: ResponseCodeEnum::forbidden,
      requestFunction: function (string $level, ?int $id) use ($iri, &$payload) {
        $this->makePatchRequest($iri, $payload);
      }
    );
  }

  public function testDelete(): void {
    $this->makeAllLoggedRequests(
      null,
      supervisorClub1Code: ResponseCodeEnum::forbidden,
      adminClub1Code: ResponseCodeEnum::no_content,
      superAdminCode: ResponseCodeEnum::no_content,
      requestFunction: function (string $level, ?int $id)  {
        $activity = ActivityFactory::createOne([
          'name' => 'Test activity to remove',
          'club' => InitStory::club_1()
        ]);
        $iri = $this->getIriFromResource($activity);

        $this->makeDeleteRequest($iri);
      }
    );
  }

  // TODO: Add activity merge test
}
