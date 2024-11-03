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

    $this->loggedAsSuperAdmin();
    $this->makeGetRequest($iri);

    self::assertResponseIsSuccessful();
    self::assertJsonContains([
      '@id' => $iri,
      'badgerToken' => $club1->getBadgerToken(),
    ]);

//    FIXME: Get an item for admin should work, for now return a 403, probably to fix in security.yaml
//    $this->loggedAsAdminClub1();
//    $this->makeGetRequest($iri);
//    self::assertResponseIsSuccessful();
//    self::assertJsonNotHasKey('badgerToken');
  }
}
