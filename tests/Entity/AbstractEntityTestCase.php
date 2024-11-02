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
  }

  public function testGetCollectionAsAdminClub1(): void {
    $this->loggedAsAdminClub1();
    $this->testGetCollectionAs(ClubRole::admin);
  }

  public function testGetCollectionAsAdminClub2(): void {
    $this->loggedAsAdminClub2();
    $this->testGetCollectionAs(ClubRole::admin);
  }

  public function testGetCollectionAsSupervisorClub1(): void {
    $this->loggedAsSupervisorClub1();
    $this->testGetCollectionAs(ClubRole::supervisor);
  }

  public function testGetCollectionAsMemberClub1(): void {
    $this->loggedAsMemberClub1();
    $this->testGetCollectionAs(ClubRole::member);
  }
}
