<?php

namespace App\Tests\Entity;

use App\Enum\ClubRole;
use App\Enum\UserRole;
use App\Tests\AbstractTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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

      ClubRole::admin->value => false,
      ClubRole::supervisor->value => false,

      ClubRole::member->value => false,
      ClubRole::badger->value => false,
    ];
  }

  protected function testGetCollectionAs(UserRole|ClubRole $role): void {
    $this->makeGetRequest($this->getRootUri());

    if (!array_key_exists($role->value, $this->getCollectionGrantedAccess())) {
      throw new \Exception("Role '$role->value' does not match the get collection access preset");
    }

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
