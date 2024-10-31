<?php

namespace App\Tests;

use App\Entity\Club;

class LoginTest extends AbstractTest {
  public function testLoginAsSuperAdmin(): void {
    $this->loggedAsSuperAdmin(); // We log as super admin
    $this->assertResponseIsSuccessful();

    // We request a super admin only route
    $this->createClientWithCredentials()->request('GET', '/clubs');
    $this->assertResponseIsSuccessful();
    self::assertMatchesResourceCollectionJsonSchema(Club::class);
  }
}
