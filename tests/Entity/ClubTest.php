<?php

namespace App\Tests\Entity;

use App\Entity\Club;
use App\Enum\ClubRole;
use App\Enum\UserRole;
use App\Factory\ClubFactory;
use App\Tests\Entity\Abstract\AbstractEntityTestCase;
use App\Tests\Story\InitStory;
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
    $this->makeAllLoggedRequests(function (string $level, ?int $id) use ($payload) {
      $this->makePostRequest($this->getRootUrl(), $payload);

      if ($level === UserRole::super_admin->value) {
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains($payload);
      } else {
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
      }
    });

  }

  public function testPatch(): void {
    $club1 = InitStory::club_1();
    $iri = $this->getIriFromResource($club1);

    $payload = [
      "name" => 'Update club de test',
    ];

    // Only super admin can update
    $this->makeAllLoggedRequests(function (string $level, ?int $club) use ($iri, $payload) {
      $this->makePatchRequest($iri, $payload);

      if ($level === UserRole::super_admin->value) {
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains($payload);
      } else {
        if ($club === 1) {
          $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        } else {
          $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
      }
    });
  }

  public function testDelete(): void {
    $club = ClubFactory::createOne([
      'name' => 'Club to delete',
      'salesEnabled' => true,
      'smtpEnabled' => true,
      'badgerToken' => 'clubbadger',
    ]);
    $iri = $this->getIriFromResource($club);

    // Only super admin can delete
    $this->makeAllLoggedRequests(function (string $level, ?int $club) use ($iri) {
      $this->makeDeleteRequest($iri);

      if ($level === UserRole::super_admin->value) {
        $this->assertResponseIsSuccessful();
      } else {
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
      }
    });
  }

  public function testCascadeDelete(): void {
    //TODO: Fix the deletion (cascade not removing all linked entities)
    // Also check that the related entities are well removed
    $club1 = InitStory::club_1();
    $iri = $this->getIriFromResource($club1);
    $this->loggedAsSuperAdmin();

    // We check user exist before
    $user = InitStory::member_club_1();
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
    $club1 = InitStory::club_1();
    $iri = $this->getIriFromResource($club1);

    $this->makeAllLoggedRequests(function (string $level, ?int $id) use ($iri, $club1) {
      $response = $this->makeGetRequest($iri);
      $this->assertResponseIsSuccessful();

      if (in_array($level, [UserRole::super_admin->value, ClubRole::admin->value])) {
        $this->assertJsonContains([
          '@id' => $iri,
          'badgerToken' => $club1->getBadgerToken(),
        ]);
      } else {
        $this->assertJsonNotHasKey('badgerToken', $response);
      }
    }, true);
  }
}
