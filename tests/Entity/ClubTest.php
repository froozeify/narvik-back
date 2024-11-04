<?php

namespace App\Tests\Entity;

use App\Entity\Club;
use App\Tests\Story\InitStory;

class ClubTest extends AbstractEntityTestCase {
  protected int $TOTAL_SUPER_ADMIN = 5;

  protected function getClassname(): string {
    return Club::class;
  }

  protected function getRootUri(): string {
    return '/clubs';
  }

  // TODO: Create test to check only superadmin and club_admin can see the `badgerToken` field

  public function testCreate(): void {
    self::markTestSkipped('to implement');
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

    // Super admin
    $this->loggedAsSuperAdmin();
    $this->makeGetRequest($iri);
    self::assertResponseIsSuccessful();
    self::assertJsonContains([
      '@id' => $iri,
      'badgerToken' => $club1->getBadgerToken(),
    ]);

    // Admin club 1
    $this->loggedAsAdminClub1();
    $this->makeGetRequest($iri);
    self::assertResponseIsSuccessful();
    self::assertJsonContains([
      '@id' => $iri,
      'badgerToken' => $club1->getBadgerToken(),
    ]);

    // Supervisor club 1
    $this->loggedAsSupervisorClub1();
    $response = $this->makeGetRequest($iri);
    self::assertResponseIsSuccessful();
    self::assertJsonNotHasKey('badgerToken', $response);

    // Member
    $this->loggedAsMemberClub1();
    $response = $this->makeGetRequest($iri);
    self::assertResponseIsSuccessful();
    self::assertJsonNotHasKey('badgerToken', $response);
  }
}
