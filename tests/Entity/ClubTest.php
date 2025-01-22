<?php

namespace App\Tests\Entity;

use App\Entity\Club;
use App\Tests\Entity\Abstract\AbstractEntityTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\ClubFactory;
use App\Tests\Story\_InitStory;
use App\Tests\Story\ActivityStory;
use Zenstruck\Foundry\Persistence\Proxy;

class ClubTest extends AbstractEntityTestCase {
  protected int $TOTAL_SUPER_ADMIN = 5;

  protected function getClassname(): string {
    return Club::class;
  }

  protected function getRootUrl(): string {
    return '/clubs';
  }

  public function testCreate(): void {
    $payload = [
      "name" => 'Club de test',
    ];

    // Only super admin can create
    $this->makeAllLoggedRequests(
      $payload,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::forbidden,
      ResponseCodeEnum::created,
      badgerClub2Code: ResponseCodeEnum::forbidden,
      requestFunction: function (string $level, ?int $id) use ($payload) {
        $this->makePostRequest($this->getRootUrl(), $payload);
      }
    );

  }

  public function testPatch(): void {
    $club1 = _InitStory::club_1();
    $iri = $this->getIriFromResource($club1);

    $payload = [
      "name" => 'Update club de test',
    ];

    // Only super admin can update
    $this->makeAllLoggedRequests(
      $payload,
      supervisorClub1Code: ResponseCodeEnum::forbidden,
      adminClub1Code: ResponseCodeEnum::forbidden,
      requestFunction: function (string $level, ?int $id) use ($iri, $payload) {
        $this->makePatchRequest($iri, $payload);
      }
    );
  }

  public function testDelete(): void {
    // Only super admin can delete
    $this->makeAllLoggedRequests(
      memberClub1Code: ResponseCodeEnum::not_found,
      supervisorClub1Code: ResponseCodeEnum::not_found,
      adminClub1Code: ResponseCodeEnum::not_found,
      superAdminCode: ResponseCodeEnum::no_content,
      badgerClub1Code: ResponseCodeEnum::not_found,
      requestFunction: function (string $level, ?int $id) {
        $club = ClubFactory::createOne([
          'name' => 'Club to delete',
          'salesEnabled' => true,
          'badgerToken' => 'clubbadger',
        ]);
        $iri = $this->getIriFromResource($club);
        $this->makeDeleteRequest($iri);
      }
    );
  }

  public function testCascadeDeletion(): void {
    $this->loggedAsSuperAdmin();

    // Activity entity is part use ClubLinkedEntityInterface with SelfClubLinkedEntityTrait
    // All entity using SelfClubLinkedEntityTrait will have the same result (cascade delete is set in the trait)
    /** @var Proxy[] $activitiesPool */
    $activitiesPool = ActivityStory::getPool("activities_club1");
    $activityIris = [];
    foreach ($activitiesPool as $activityClub) {
      $activityIris[] = $this->getIriFromResource($activityClub->_real());
    }

    $club1 = _InitStory::club_1();
    $clubIri = $this->getIriFromResource($club1);

    // We check user exist before
    $user = _InitStory::USER_member_club_1();
    $userMembership = $user->getMemberships()->get(0);
    $userMemberIri = $this->getIriFromResource($userMembership->getMember());
    $this->makeGetRequest($userMemberIri);
    $this->assertResponseIsSuccessful();
    // Activity exist
    foreach ($activityIris as $activityIri) {
      $this->makeGetRequest($activityIri);
      $this->assertResponseIsSuccessful();
    }

    // We make the deletion
    $this->makeDeleteRequest($clubIri);
    $this->assertResponseIsSuccessful();

    // We verify all are well removed
    $this->makeGetRequest($userMemberIri);
    $this->assertResponseStatusCodeSame(ResponseCodeEnum::not_found->value);
    foreach ($activityIris as $activityIri) {
      $this->makeGetRequest($activityIri);
      $this->assertResponseStatusCodeSame(ResponseCodeEnum::not_found->value);
    }
  }

  public function testBadgerTokenFieldVisibility(): void {
    $club1 = _InitStory::club_1();
    $iri = $this->getIriFromResource($club1);

    $payloadMatch = [
      '@id' => $iri,
      'badgerToken' => $club1->getBadgerToken(),
    ];

    $success = [
      'loggedAsSuperAdmin',
      'loggedAsAdminClub1',
    ];
    foreach ($success as $function) {
      $this->$function();
      $this->makeGetRequest($iri);
      $this->assertJsonContains($payloadMatch);
    }

    $denied = [
      'loggedAsSupervisorClub1',
      'loggedAsMemberClub1',
    ];
    foreach ($denied as $function) {
      $this->$function();
      $response = $this->makeGetRequest($iri);
      $this->assertJsonNotHasKey('badgerToken', $response);
    }
  }
}
