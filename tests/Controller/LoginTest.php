<?php

namespace App\Tests\Controller;

use App\Factory\UserFactory;
use App\Tests\AbstractTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends AbstractTestCase {
  public function testSuccessful(): void {
    $this->makeAllLoggedRequests(function () {
      $this->assertResponseIsSuccessful();
    });
  }

  public function testLoginAsNotActivated(): void {
    // We create a not activated account
    $notActivated = UserFactory::createOne([
      'email' => 'notactivated@user.fr',
      'plainPassword' => 'testuser123',
      'accountActivated' => false,
    ]);
    $notActivated->_enableAutoRefresh();

    $this->loggedAs("notactivated@user.fr", "testuser123");
    $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

    // We update the user
    $notActivated->setAccountActivated(true);
    $notActivated->_save();

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
