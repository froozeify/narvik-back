<?php

namespace App\Tests\Controller;

use App\Entity\Club;
use App\Tests\AbstractTest;

class LoginTest extends AbstractTest {
  public function testLoginAsSuperAdmin(): void {
    $this->loggedAsSuperAdmin(); // We log as super admin
    $this->assertResponseIsSuccessful();

    // We request a super admin only route
    $this->createClientWithCredentials()->request('GET', '/clubs');
    $this->assertResponseIsSuccessful();
    self::assertMatchesResourceCollectionJsonSchema(Club::class);
  }

//  public function testLoginAsClubAdmin(): void { }
//
//  public function testLoginAsClubSupervisor(): void { }
//
//  public function testLoginAsClubMember(): void { }
//
//  public function testLoginAsNotActivated(): void { }

  /**
   * Account does not exist
   *
   * @return void
   */
  public function testLoginAsUnknown(): void {
    $this->loggedAs("notexisting@test.fr", "test");
    $this->assertResponseStatusCodeSame(401);
  }
}
