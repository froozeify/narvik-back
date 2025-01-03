<?php

namespace App\Tests\Entity\ClubDependent;

use App\Entity\Club;
use App\Entity\ClubDependent\Member;
use App\Entity\ClubDependent\MemberSeason;
use App\Enum\ClubRole;
use App\Tests\Entity\Abstract\AbstractEntityClubLinkedTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\MemberFactory;
use App\Tests\Story\_InitStory;
use App\Tests\Story\SeasonStory;

class MemberSeasonTest extends AbstractEntityClubLinkedTestCase {

  protected int $TOTAL_SUPER_ADMIN = 1;
  protected int $TOTAL_ADMIN_CLUB_1 = 1;
  protected int $TOTAL_ADMIN_CLUB_2 = 0; // Since we are forced on member club 1
  protected int $TOTAL_SUPERVISOR_CLUB_1 = 1;

  private Member $selectedMember;

  public function setUp(): void {
    parent::setUp();
    $this->selectedMember = _InitStory::MEMBER_member_club_1();
  }

  protected function getClassname(): string {
    return MemberSeason::class;
  }

  protected function getRootUrl(): string {
    // The "root url" is specific since it's a sub-resource of member
    // This should never be call
    throw new \Exception("Subresource! getRootUrl() must not be call.");
  }

  protected function getRootWClubUrl(Club $club): string {
    return $this->getIriFromResource($club) . "/members/{$this->selectedMember->getUuid()}/seasons";
  }

  public function testCreate(): void {
    $club1 = _InitStory::club_1();

    $count = 2015;
    $this->makeAllLoggedRequests(
      $payloadCheck,
      supervisorClub1Code: ResponseCodeEnum::created,
      adminClub1Code: ResponseCodeEnum::created,
      adminClub2Code: ResponseCodeEnum::bad_request,
      superAdminCode: ResponseCodeEnum::created,
      badgerClub2Code: ResponseCodeEnum::bad_request,
      requestFunction: function (string $level, ?int $id) use ($club1, &$payloadCheck, &$count) {
        // We update for each test since the name must be unique
        $sn = "season_" . $count;
        $season = SeasonStory::$sn();

        $payload = [
          "member" => $this->getIriFromResource($this->selectedMember),
          "season" => $this->getIriFromResource($season),
        ];

        $payloadCheck = $payload;
        $payloadCheck["member"] = [
          "@id" => $this->getIriFromResource($this->selectedMember),
        ];
        $payloadCheck["season"] = [
          "@id" => $this->getIriFromResource($season),
        ];

        $this->makePostRequest($this->getRootWClubUrl($club1), $payload);
        $count++;
      },
    );
  }

  public function testPatch(): void {
    self::markTestSkipped();
//    $this->makeAllLoggedRequests(
//      requestFunction: function (string $level, ?int $id) {
//        $member = MemberFactory::createOne([
//          'club' => _InitStory::club_1(),
//        ]);
//        $iri = $this->getIriFromResource($member);
//
//        $payload = [
//          "firstname" => 'Updated name',
//        ];
//
//        $this->makePatchRequest($iri, $payload);
//      },
//    );
  }

  public function testDelete(): void {
    self::markTestSkipped();
//    $this->makeAllLoggedRequests(
//      supervisorClub1Code: ResponseCodeEnum::forbidden,
//      adminClub1Code: ResponseCodeEnum::no_content,
//      superAdminCode: ResponseCodeEnum::no_content,
//      requestFunction: function (string $level, ?int $id) {
//        $member = MemberFactory::createOne([
//          'club' => _InitStory::club_1(),
//        ]);
//        $iri = $this->getIriFromResource($member);
//        $this->makeDeleteRequest($iri);
//      },
//    );
  }
}
