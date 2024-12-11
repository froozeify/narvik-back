<?php

namespace App\Tests\Controller;

use App\Tests\AbstractTestCase;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends AbstractTestCase {
  public function testSuccessful(): void {
    $this->makeAllLoggedRequests(
      null,
      ResponseCodeEnum::ok,
      ResponseCodeEnum::ok,
      ResponseCodeEnum::ok,
      ResponseCodeEnum::ok,
      ResponseCodeEnum::ok,
      ResponseCodeEnum::ok,
      ResponseCodeEnum::ok,
    );
  }

  public function testLoginAsNotActivated(): void {
    // We create a not activated account
    $notActivated = UserFactory::createOne([
      'email' => 'notactivated@user.fr',
      'plainPassword' => 'testuser123',
      'accountActivated' => false,
    ]);

    $this->loggedAs("notactivated@user.fr", "testuser123");
    $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

    // We enable the account
    $this->loggedAsSuperAdmin();
    $iri = $this->getIriFromResource($notActivated);
    $this->makePatchRequest($iri, ['accountActivated' => true]);

    // Account is now activated, the user can log in
    $this->loggedAs("notactivated@user.fr", "testuser123");
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);
  }

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
