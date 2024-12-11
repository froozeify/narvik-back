<?php

namespace App\Tests\Entity\ClubDependent;

use App\Entity\ClubDependent\Activity;
use App\Entity\ClubDependent\Member;
use App\Enum\ClubRole;
use App\Tests\Entity\Abstract\AbstractEntityClubLinkedTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\ActivityFactory;
use App\Tests\Factory\MemberFactory;
use App\Tests\Story\_InitStory;
use App\Tests\Story\ActivityStory;
use Zenstruck\Foundry\Persistence\Proxy;

class MemberTest extends AbstractEntityClubLinkedTestCase {
  protected int $TOTAL_SUPER_ADMIN = 3;
  protected int $TOTAL_ADMIN_CLUB_1 = 3;
  protected int $TOTAL_ADMIN_CLUB_2 = 2;
  protected int $TOTAL_SUPERVISOR_CLUB_1 = 3;

  protected function getClassname(): string {
    return Member::class;
  }

  protected function getRootUrl(): string {
    return "/members";
  }

  public function testCreate(): void {
    $club1 = _InitStory::club_1();
    $iri = $this->getIriFromResource($club1);

    $payload = [
      "firstname" => "firstname",
      "lastname" => "lastname",
    ];

    $payloadCheck = $payload;
    $payloadCheck['firstname'] = ucfirst($payload['firstname']);
    $payloadCheck['lastname'] = strtoupper($payload['lastname']);
    // For the check we update the payload value
    $payloadCheck["club"] = [
      '@id' => $iri,
    ];
    $payloadCheck["fullName"] = $payloadCheck['lastname'] . " " . $payloadCheck['firstname'];

    $this->makeAllLoggedRequests(
      $payloadCheck,
      supervisorClub1Code: ResponseCodeEnum::created,
      adminClub1Code: ResponseCodeEnum::created,
      adminClub2Code: ResponseCodeEnum::forbidden,
      superAdminCode: ResponseCodeEnum::created,
      badgerClub2Code: ResponseCodeEnum::forbidden,
      requestFunction: function (string $level, ?int $id) use ($club1, $payload) {
        $this->makePostRequest($this->getRootWClubUrl($club1), $payload);
      },
    );
  }

  public function testPatch(): void {
    $this->makeAllLoggedRequests(
      requestFunction: function (string $level, ?int $id) {
        $member = MemberFactory::createOne([
          'club' => _InitStory::club_1(),
        ]);
        $iri = $this->getIriFromResource($member);

        $payload = [
          "firstname" => 'Updated name',
        ];

        $this->makePatchRequest($iri, $payload);
      },
    );
  }

  public function testDelete(): void {
    $this->makeAllLoggedRequests(
      supervisorClub1Code: ResponseCodeEnum::forbidden,
      adminClub1Code: ResponseCodeEnum::no_content,
      superAdminCode: ResponseCodeEnum::no_content,
      requestFunction: function (string $level, ?int $id) {
        $member = MemberFactory::createOne([
          'club' => _InitStory::club_1(),
        ]);
        $iri = $this->getIriFromResource($member);
        $this->makeDeleteRequest($iri);
      },
    );
  }

  public function testSearchMember(): void {
    $club = _InitStory::club_1();
    $memberClub1 = _InitStory::MEMBER_member_club_1();
    $memberClub2 = _InitStory::MEMBER_member_club_2();

    $this->loggedAsBadgerClub1();
    $response = $this->makePostRequest($this->getRootWClubUrl($club) . "/-/search", [
      'query' => $memberClub1->getFullName()
    ]);
    $this->assertResponseStatusCodeSame(ResponseCodeEnum::ok->value);
    $this->assertCount(1, $response->toArray());

    $response = $this->makePostRequest($this->getRootWClubUrl($club) . "/-/search", [
      'query' => $memberClub2->getFullName()
    ]);
    $this->assertResponseStatusCodeSame(ResponseCodeEnum::ok->value);
    $this->assertCount(0, $response->toArray());
  }

  // TODO: Add custom route tests

}
