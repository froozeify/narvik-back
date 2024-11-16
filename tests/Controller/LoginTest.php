<?php

namespace App\Tests\Controller;

use App\Tests\AbstractTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends AbstractTestCase {
  public function testSuccessful(): void {
    $this->makeAllLoggedRequests(function () {
      $this->assertResponseIsSuccessful();
    });
  }

//  TODO: Add login test as a not activated account, should fail
//  public function testLoginAsNotActivated(): void { }

  public function testLoginWithWrongPassword(): void {
    $this->loggedAs("admin@admin.com", "wrong");
    $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

    // We request a PRIVATE resource
    $this->makeGetRequest('/clubs');
    $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
  }

  /**
   * Account does not exist
   *
   * @return void
   */
  public function testLoginAsUnknown(): void {
    $this->loggedAs("notexisting@test.fr", "test");
    $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
  }
}
