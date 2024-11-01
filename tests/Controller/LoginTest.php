<?php

namespace App\Tests\Controller;

use App\Tests\AbstractTestCase;

class LoginTest extends AbstractTestCase {
  public function testLoginAsSuperAdmin(): void {
    $this->loggedAsSuperAdmin(); // We log as super admin
    $this->assertResponseIsSuccessful();
  }

//  public function testLoginAsClubAdmin(): void { }
//
//  public function testLoginAsClubSupervisor(): void { }
//
//  public function testLoginAsClubMember(): void { }
//
//  public function testLoginAsNotActivated(): void { }

  public function testLoginWithWrongPassword(): void {
    $this->loggedAs("admin@admin.com", "wrong");
    $this->assertResponseStatusCodeSame(401);

    // We request a PRIVATE resource
    $this->makeGetRequest('/activities');
    $this->assertResponseStatusCodeSame(401);
  }

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
