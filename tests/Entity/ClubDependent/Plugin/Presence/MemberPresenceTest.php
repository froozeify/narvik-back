<?php

namespace App\Tests\Entity\ClubDependent\Plugin\Presence;

use App\Entity\ClubDependent\Plugin\Presence\MemberPresence;
use App\Enum\ClubRole;
use App\Message\CerberePresencesDateMessage;
use App\Message\ItacSecondaryClubMembersMessage;
use App\Tests\Entity\Abstract\AbstractEntityClubLinkedTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\ActivityFactory;
use App\Tests\Factory\ExternalPresenceFactory;
use App\Tests\Factory\MemberFactory;
use App\Tests\Factory\MemberPresenceFactory;
use App\Tests\Story\_InitStory;
use App\Tests\Story\ActivityStory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Messenger\Test\InteractsWithMessenger;
use function Zenstruck\Foundry\faker;

class MemberPresenceTest extends AbstractEntityClubLinkedTestCase {
  use InteractsWithMessenger;

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
    $payloadCheck = [];
    $this->makeAllLoggedRequests(
      $payloadCheck,
      supervisorClub1Code: ResponseCodeEnum::created,
      adminClub1Code: ResponseCodeEnum::created,
      adminClub2Code: ResponseCodeEnum::bad_request,
      superAdminCode: ResponseCodeEnum::created,
      badgerClub1Code: ResponseCodeEnum::created,
      badgerClub2Code: ResponseCodeEnum::bad_request,
      requestFunction: function (string $level, ?int $id) use (&$payloadCheck) {
        $club1 = _InitStory::club_1();

        // New member to be sure it has no presence registered to him
        $member = MemberFactory::createOne([
          'club' => $club1,
          'email' => 'membertest@club1.fr',
        ]);

        $clubIri = $this->getIriFromResource($club1);
        $memberIri = $this->getIriFromResource($member);

        $payload = [
          "member" => $memberIri,
        ];

        $payloadCheck = [
          "club" => [
            '@id' => $clubIri,
          ],
          "member" => [
            '@id' => $memberIri,
          ]
        ];

        $this->makePostRequest($this->getRootWClubUrl($club1), $payload);
      },
    );
  }

  public function testPatch(): void {
    $payloadCheck = [];
    $this->makeAllLoggedRequests(
      $payloadCheck,
      badgerClub1Code: ResponseCodeEnum::ok,
      requestFunction: function (string $level, ?int $id) use (&$payloadCheck) {
        $memberPresence = MemberPresenceFactory::createOne([
          'club' => _InitStory::club_1(),
          'member' => MemberFactory::createOne(),
        ]);

        $payloadCheck = [
          "activities" => []
        ];

        // We remove all activities
        $this->makePatchRequest($this->getIriFromResource($memberPresence), ["activities" => []]);
      },
    );
  }

  public function testDelete(): void {
    $this->makeAllLoggedRequests(
      $payloadCheck,
      supervisorClub1Code: ResponseCodeEnum::no_content,
      adminClub1Code: ResponseCodeEnum::no_content,
      adminClub2Code: ResponseCodeEnum::not_found,
      superAdminCode: ResponseCodeEnum::no_content,
      badgerClub1Code: ResponseCodeEnum::no_content,
      badgerClub2Code: ResponseCodeEnum::not_found,
      requestFunction: function (string $level, ?int $id) use (&$payloadCheck) {
        $memberPresence = MemberPresenceFactory::createOne([
          'club' => _InitStory::club_1(),
          'member' => _InitStory::MEMBER_member_club_1(),
        ]);
        $this->makeDeleteRequest($this->getIriFromResource($memberPresence));
      },
    );
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

  public function testImportPresencesFromCerbere(): void {
    $club = _InitStory::club_1();

    $transport = $this->transport('async_low');
    $transport->queue()->assertEmpty();

    $file = new UploadedFile(__DIR__ . '/../../../../fixtures/presence-cerbere.xls', 'presence-cerbere.xls');

    $this->loggedAsAdminClub1();
    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1, $response->toArray()['member']);

    $response = $this->makePostRequest($this->getRootWClubUrl($club) . "/-/from-cerbere", [
      '_not_json' => true,
      'headers' => ['Content-Type' => 'multipart/form-data'],
      'extra' => [
        'files' => [
          'file' => $file,
        ],
      ],
    ]);

    $this->assertResponseIsSuccessful();
    $this->assertEquals(2, $response->toArray()['days']);
    $transport->queue()->assertCount(2);
    $transport->queue()->assertContains(CerberePresencesDateMessage::class, 2);

    // We consume the queue
    $transport->process();
    $transport->queue()->assertEmpty();

    // 2 new presences (and 1 external but will be tested in ExternalPresenceTest)
    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1 + 2, $response->toArray()['member']);

    // Running the import a second time should not change the count
    $response = $this->makePostRequest($this->getRootWClubUrl($club) . "/-/from-cerbere", [
      '_not_json' => true,
      'headers' => ['Content-Type' => 'multipart/form-data'],
      'extra' => [
        'files' => [
          'file' => $file,
        ],
      ],
    ]);

    $this->assertResponseIsSuccessful();
    $this->assertEquals(2, $response->toArray()['days']);
    // We consume the queue
    $transport->process();
    $transport->queue()->assertEmpty();

    // 2 new presences (and 1 external but will be tested in ExternalPresenceTest)
    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1 + 2, $response->toArray()['member']);
  }

  public function testImportPresencesFromCSV(): void {
    $club = _InitStory::club_1();

    $file = new UploadedFile(__DIR__ . '/../../../../fixtures/narvik-presences.csv', 'narvik-presences.csv');

    $this->loggedAsAdminClub1();
    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1, $response->toArray()['member']);

    $response = $this->makePostRequest($this->getRootWClubUrl($club) . "/-/from-csv", [
      '_not_json' => true,
      'headers' => ['Content-Type' => 'multipart/form-data'],
      'extra' => [
        'files' => [
          'file' => $file,
        ],
      ],
    ]);

    $this->assertResponseIsSuccessful();

    $this->assertCount(2, $response->toArray()['created']);
    $this->assertCount(0, $response->toArray()['warnings']);
    $this->assertCount(1, $response->toArray()['errors']);

    // 2 new presences
    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1 + 2, $response->toArray()['member']);

    // Running the import a second time should not change the count
    $response = $this->makePostRequest($this->getRootWClubUrl($club) . "/-/from-csv", [
      '_not_json' => true,
      'headers' => ['Content-Type' => 'multipart/form-data'],
      'extra' => [
        'files' => [
          'file' => $file,
        ],
      ],
    ]);
    $this->assertResponseIsSuccessful();
    $this->assertCount(0, $response->toArray()['created']);
    $this->assertCount(2, $response->toArray()['warnings']); // Already registered
    $this->assertCount(1, $response->toArray()['errors']);

    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1 + 2, $response->toArray()['member']);
  }

  public function testImportPresencesFromExternalPresences(): void {
    $club = _InitStory::club_1();
    ExternalPresenceFactory::createOne([
      'licence' => '10000001',
      'date' => \DateTimeImmutable::createFromFormat('Y-m-d', '2020-01-01'),
    ]);

    $this->loggedAsAdminClub1();
    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1, $response->toArray()['member']);

    $response = $this->makePostRequest($this->getRootWClubUrl($club) . "/-/import-from-external-presences");
    $this->assertResponseIsSuccessful();
    $this->assertEquals(1, $response->toArray()['imported']);

    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1 + 1, $response->toArray()['member']);

    $response = $this->makePostRequest($this->getRootWClubUrl($club) . "/-/import-from-external-presences");
    $this->assertResponseIsSuccessful();
    $this->assertEquals(0, $response->toArray()['imported']);

    $response = $this->makeGetRequest($this->getRootWClubUrl($club));
    $this->assertCount($this->TOTAL_ADMIN_CLUB_1 + 1, $response->toArray()['member']);
  }
}
