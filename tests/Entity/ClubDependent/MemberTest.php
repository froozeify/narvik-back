<?php

namespace App\Tests\Entity\ClubDependent;

use App\Entity\ClubDependent\Activity;
use App\Entity\ClubDependent\Member;
use App\Enum\ClubRole;
use App\Message\ItacMembersMessage;
use App\Message\ItacSecondaryClubMembersMessage;
use App\Tests\Entity\Abstract\AbstractEntityClubLinkedTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\ActivityFactory;
use App\Tests\Factory\MemberFactory;
use App\Tests\Story\_InitStory;
use App\Tests\Story\ActivityStory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class MemberTest extends AbstractEntityClubLinkedTestCase {
  use InteractsWithMessenger;

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

  public function testImportItacMembers(): void {
    $club = _InitStory::club_1();

    $this->transport('async_medium')->queue()->assertEmpty();

    $file = new UploadedFile(__DIR__ . '/../../fixtures/itac-members.csv', 'itac-members.csv');

    $this->loggedAsAdminClub1();
    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1, $response->toArray()['member']);

    $response = $this->makePostRequest($this->getRootWClubUrl($club) . "/-/from-itac", [
      '_not_json' => true,
      'headers' => ['Content-Type' => 'multipart/form-data'],
      'extra' => [
        'files' => [
          'file' => $file
        ]
      ]
    ]);

    $this->assertResponseIsSuccessful();
    $this->assertEquals(2, $response->toArray()['lines']);
    $this->transport('async_medium')->queue()->assertCount(1);
    $this->transport('async_medium')->queue()->assertContains(ItacMembersMessage::class, 1);

    // We consume the queue
    $this->transport('async_medium')->process();
    $this->transport('async_medium')->queue()->assertEmpty();

    // 2 new members
    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1 + 2, $response->toArray()['member']);

    // Running the import a second time should not change the count
    $response = $this->makePostRequest($this->getRootWClubUrl($club) . "/-/from-itac", [
      '_not_json' => true,
      'headers' => ['Content-Type' => 'multipart/form-data'],
      'extra' => [
        'files' => [
          'file' => $file
        ]
      ]
    ]);

    $this->assertResponseIsSuccessful();
    $this->assertEquals(2, $response->toArray()['lines']);
    // We consume the queue
    $this->transport('async_medium')->process();
    $this->transport('async_medium')->queue()->assertEmpty();

    // 2 new members
    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1 + 2, $response->toArray()['member']);
  }

  public function testImportItacSecondaryMembers(): void {
    $club = _InitStory::club_1();

    $transport = $this->transport('async_low');
    $transport->queue()->assertEmpty();

    $file = new UploadedFile(__DIR__ . '/../../fixtures/itac-secondary-members.csv', 'itac-secondary-members.csv');

    $this->loggedAsAdminClub1();
    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1, $response->toArray()['member']);

    $response = $this->makePostRequest($this->getRootWClubUrl($club) . "/-/secondary-from-itac", [
      '_not_json' => true,
      'headers' => ['Content-Type' => 'multipart/form-data'],
      'extra' => [
        'files' => [
          'file' => $file
        ]
      ]
    ]);

    $this->assertResponseIsSuccessful();
    $this->assertEquals(2, $response->toArray()['lines']);
    $transport->queue()->assertCount(1);
    $transport->queue()->assertContains(ItacSecondaryClubMembersMessage::class, 1);

    // We consume the queue
    $transport->process();
    $transport->queue()->assertEmpty();

    // 2 new members
    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1 + 2, $response->toArray()['member']);

    // Running the import a second time should not change the count
    $response = $this->makePostRequest($this->getRootWClubUrl($club) . "/-/secondary-from-itac", [
      '_not_json' => true,
      'headers' => ['Content-Type' => 'multipart/form-data'],
      'extra' => [
        'files' => [
          'file' => $file
        ]
      ]
    ]);

    $this->assertResponseIsSuccessful();
    $this->assertEquals(2, $response->toArray()['lines']);
    // We consume the queue
    $transport->process();
    $transport->queue()->assertEmpty();

    // 2 new members
    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1 + 2, $response->toArray()['member']);
  }
}
