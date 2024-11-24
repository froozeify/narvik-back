<?php

namespace App\Tests\Entity;

use App\Entity\Club;
use App\Tests\Entity\Abstract\AbstractEntityTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\ClubFactory;
use App\Tests\Story\_InitStory;
use Symfony\Component\HttpFoundation\Response;

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
      null,
      ResponseCodeEnum::not_found,
      ResponseCodeEnum::not_found,
      ResponseCodeEnum::not_found,
      superAdminCode: ResponseCodeEnum::no_content,
      requestFunction: function (string $level, ?int $id) {
        $club = ClubFactory::createOne([
          'name' => 'Club to delete',
          'salesEnabled' => true,
          'smtpEnabled' => true,
          'badgerToken' => 'clubbadger',
        ]);
        $iri = $this->getIriFromResource($club);
        $this->makeDeleteRequest($iri);
      }
    );
  }

  public function testCascadeDelete(): void {
    //TODO: Fix the deletion (cascade not removing all linked entities)
    // Also check that the related entities are well removed
    $club1 = _InitStory::club_1();
    $iri = $this->getIriFromResource($club1);
    $this->loggedAsSuperAdmin();

    // We check user exist before
    $user = _InitStory::member_club_1();
    $userMembership = $user->getMemberships()->get(0);
    $userMemberIri = $this->getIriFromResource($userMembership->getMember());

    // TODO: Add get member presences (and check well removed after)

    $this->makeGetRequest($userMemberIri);
    $this->assertResponseIsSuccessful();

    $this->makeDeleteRequest($iri);
    $this->assertResponseIsSuccessful();

    $this->makeGetRequest($userMemberIri);
    $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
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
