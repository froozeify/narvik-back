<?php

namespace App\Tests\Entity\ClubDependent;

use App\Entity\ClubDependent\Metric;
use App\Tests\Entity\Abstract\AbstractEntityClubLinkedTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Story\_InitStory;

class MetricTest extends AbstractEntityClubLinkedTestCase {

  protected int $TOTAL_SUPER_ADMIN = 4;
  protected int $TOTAL_ADMIN_CLUB_1 = 4;
  protected int $TOTAL_ADMIN_CLUB_2 = 4;
  protected int $TOTAL_SUPERVISOR_CLUB_1 = 4;

  protected function getClassname(): string {
    return Metric::class;
  }

  protected function getRootUrl(): string {
    return "/metrics";
  }

  public function testCreate(): void {
    $club1 = _InitStory::club_1();

    $this->makeAllLoggedRequests(
      memberClub1Code: ResponseCodeEnum::not_allowed,
      supervisorClub1Code: ResponseCodeEnum::not_allowed,
      adminClub1Code: ResponseCodeEnum::not_allowed,
      adminClub2Code: ResponseCodeEnum::not_allowed,
      superAdminCode: ResponseCodeEnum::not_allowed,
      badgerClub1Code: ResponseCodeEnum::not_allowed,
      badgerClub2Code: ResponseCodeEnum::not_allowed,
      requestFunction: function (string $level, ?int $id) use ($club1) {
        $this->makePostRequest($this->getRootWClubUrl($club1));
      },
    );
  }

  public function testPatch(): void {
    $club1 = _InitStory::club_1();

    $this->makeAllLoggedRequests(
      memberClub1Code: ResponseCodeEnum::not_allowed,
      supervisorClub1Code: ResponseCodeEnum::not_allowed,
      adminClub1Code: ResponseCodeEnum::not_allowed,
      adminClub2Code: ResponseCodeEnum::not_allowed,
      superAdminCode: ResponseCodeEnum::not_allowed,
      badgerClub1Code: ResponseCodeEnum::not_allowed,
      badgerClub2Code: ResponseCodeEnum::not_allowed,
      requestFunction: function (string $level, ?int $id) use ($club1) {
        $this->makePatchRequest($this->getRootWClubUrl($club1));
      },
    );
  }

  public function testDelete(): void {
    $club1 = _InitStory::club_1();

    $this->makeAllLoggedRequests(
      memberClub1Code: ResponseCodeEnum::not_allowed,
      supervisorClub1Code: ResponseCodeEnum::not_allowed,
      adminClub1Code: ResponseCodeEnum::not_allowed,
      adminClub2Code: ResponseCodeEnum::not_allowed,
      superAdminCode: ResponseCodeEnum::not_allowed,
      badgerClub1Code: ResponseCodeEnum::not_allowed,
      badgerClub2Code: ResponseCodeEnum::not_allowed,
      requestFunction: function (string $level, ?int $id) use ($club1) {
        $this->makeDeleteRequest($this->getRootWClubUrl($club1));
      },
    );
  }

  public function testSuperAdminGetAllStatsMerged(): void {
    $iri = $this->getRootUrl();
    $iriItem = $iri . "/members";

    $this->loggedAsAdminClub1();
    $this->makeGetRequest($iri);
    $this->assertResponseStatusCodeSame(ResponseCodeEnum::forbidden->value);
    $this->makeGetRequest($iriItem);
    $this->assertResponseStatusCodeSame(ResponseCodeEnum::forbidden->value);

    $this->loggedAsSuperAdmin();
    $this->makeGetRequest($iri);
    $this->assertResponseIsSuccessful();
    $this->makeGetRequest($iriItem);
    $this->assertResponseIsSuccessful();
  }

}
