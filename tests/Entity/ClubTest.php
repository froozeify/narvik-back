<?php

namespace App\Tests\Entity;

use App\Entity\Club;
use App\Enum\ClubRole;
use App\Enum\UserRole;
use App\Tests\Story\InitStory;
use Symfony\Component\HttpFoundation\Response;

class ClubTest extends AbstractEntityTestCase {
  protected int $TOTAL_SUPER_ADMIN = 5;

  protected function getClassname(): string {
    return Club::class;
  }

  protected function getRootUri(): string {
    return '/clubs';
  }

  public function testCreate(): void {
    $payload = [
      "name" => 'Club de test',
    ];

    // Only super admin can create
    $this->makeAllLoggedRequests(function (string $level, ?int $id) use ($payload) {
      $this->makePostRequest($this->getRootUri(), $payload);

      if ($level === UserRole::super_admin->value) {
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains($payload);
      } else {
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
      }
    });

  }

  public function testPatch(): void {
    self::markTestSkipped('to implement');
  }

  public function testDelete(): void {
    self::markTestSkipped('to implement');
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
