<?php

namespace App\Tests;

use App\Entity\Club\Club;

class LoginTest extends AbstractTest {
  public function testLoginAsSuperAdmin(): void {
    $this->loginAsSuperAdmin(); // We log as super admin

    // We request a super admin only route
    $this->createClientWithCredentials()->request('GET', '/clubs');
    $this->assertResponseIsSuccessful();
    self::assertMatchesResourceCollectionJsonSchema(Club::class);
  }
}
