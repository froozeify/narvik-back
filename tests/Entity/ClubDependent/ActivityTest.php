<?php

namespace App\Tests\Entity\ClubDependent;

use App\Entity\ClubDependent\Activity;
use App\Enum\ClubRole;
use App\Enum\UserRole;
use App\Tests\Entity\Abstract\AbstractEntityClubLinkedTestCase;
use App\Tests\Story\ActivityStory;
use App\Tests\Story\InitStory;
use Symfony\Component\HttpFoundation\Response;

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
    $club1 = InitStory::club_1();
    $iri = $this->getIriFromResource($club1);

    $payload = [
      "name" => 'Test activity',
      "club" => $iri,
    ];

    // Only super admin and Club Admin can make the request
    $this->makeAllLoggedRequests(function (string $level, ?int $id) use ($payload) {
      $this->makePostRequest($this->getRootUrl(), $payload);

      // For the check we update the payload value
      $payload["club"] = [
        '@id' => $payload["club"],
      ];

      if (in_array($level, [UserRole::super_admin->value, ClubRole::admin->value])) {
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains($payload);
      } else {
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
      }
    }, true);
  }

  public function testPatch(): void {
    $activity = ActivityStory::getRandom("activities_club1");
    $iri = $this->getIriFromResource($activity);

    $payload = [
      "name" => 'Update activity'
    ];

    // Only super admin and Club Admin can make the request
    $this->makeAllLoggedRequests(function (string $level, ?int $id) use ($iri, $payload) {
      $payload["name"] .= $id;
      $this->makePatchRequest($iri, $payload);

      if (in_array($level, [UserRole::super_admin->value, ClubRole::admin->value])) {
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains($payload);
      } else {
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
      }
    }, true);
  }

  public function testDelete(): void {
    $activity = ActivityStory::getRandom("activities_club1");
    $iri = $this->getIriFromResource($activity);

    // Admin club 2 can't delete
    $this->loggedAsAdminClub2();
    $this->makeDeleteRequest($iri);
    $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

    // Supervisor club 1 can't
    $this->loggedAsSupervisorClub1();
    $this->makeDeleteRequest($iri);
    $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    // Admin club 1 can
    $this->loggedAsAdminClub1();
    $this->makeDeleteRequest($iri);
    $this->assertResponseIsSuccessful();
  }

  // TODO: Add activity merge test

}
