<?php

namespace App\Tests\Entity;

use App\Enum\UserRole;
use App\Tests\AbstractTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractEntityTestCase extends AbstractTestCase {
  abstract protected function getClassname() : string;
  abstract protected function getRootUri() : string;

  /**
   * Mapping of the collection response code
   *
   * @return array{string, bool}
   */
  protected function getCollectionGrantedAccess() : array {
    return [
      UserRole::super_admin->value => true,
      UserRole::admin->value => true,

      UserRole::member->value => false,
      UserRole::badger->value => false,
    ];
  }

  private function testGetCollectionAs(UserRole $role): void {
    $this->makeGetRequest($this->getRootUri());

    if (!$this->getCollectionGrantedAccess()[$role->value]) {
      self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
      return;
    }

    self::assertResponseIsSuccessful();
    self::assertMatchesResourceCollectionJsonSchema($this->getClassname());
  }

  public function testGetCollectionAsSuperAdmin(): void {
    $this->loggedAsSuperAdmin();
    $this->testGetCollectionAs(UserRole::super_admin);
    return;
  }
}
