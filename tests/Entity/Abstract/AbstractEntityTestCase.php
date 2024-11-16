<?php

namespace App\Tests\Entity;

use App\Enum\ClubRole;
use App\Enum\UserRole;
use App\Tests\AbstractTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * For a better comprehension on how to create the entities test
 * @see https://api-platform.com/docs/v3.0/distribution/testing/
 */
abstract class AbstractEntityTestCase extends AbstractTestCase {
  protected int $TOTAL_SUPER_ADMIN = 0;
  protected int $TOTAL_ADMIN_CLUB_1 = 0;
  protected int $TOTAL_ADMIN_CLUB_2 = 0;
  protected int $TOTAL_SUPERVISOR_CLUB_1 = 0;
  protected int $TOTAL_MEMBER_CLUB_1 = 0;

  abstract protected function getClassname() : string;
  abstract protected function getRootUri() : string;

  abstract public function testCreate(): void;
  abstract public function testPatch(): void;
  abstract public function testDelete(): void;

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

  protected function testGetCollectionAs(UserRole|ClubRole $role): ResponseInterface {
    $response = $this->makeGetRequest($this->getRootUri());

    if (!$this->getCollectionGrantedAccess()[$role->value]) {
      self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
      return $response;
    }

    self::assertResponseIsSuccessful();
    self::assertMatchesResourceCollectionJsonSchema($this->getClassname());
    return $response;
  }

  public function testGetCollectionAsSuperAdmin(): ResponseInterface {
    $this->loggedAsSuperAdmin();
    $response = $this->testGetCollectionAs(UserRole::super_admin);
    if ($this->getCollectionGrantedAccess()[UserRole::super_admin->value]) {
      $this->assertCount($this->TOTAL_SUPER_ADMIN, $response->toArray()['member']);
    }
    return $response;
  }

  public function testGetCollectionAsAdminClub1(): ResponseInterface {
    $this->loggedAsAdminClub1();
    $response = $this->testGetCollectionAs(ClubRole::admin);
    if ($this->getCollectionGrantedAccess()[ClubRole::admin->value]) {
      $this->assertCount($this->TOTAL_ADMIN_CLUB_1, $response->toArray()['member']);
    }
    return $response;
  }

  public function testGetCollectionAsAdminClub2(): ResponseInterface {
    $this->loggedAsAdminClub2();
    $response = $this->testGetCollectionAs(ClubRole::admin);
    if ($this->getCollectionGrantedAccess()[ClubRole::admin->value]) {
      $this->assertCount($this->TOTAL_ADMIN_CLUB_2, $response->toArray()['member']);
    }
    return $response;
  }

  public function testGetCollectionAsSupervisorClub1(): ResponseInterface {
    $this->loggedAsSupervisorClub1();
    $response = $this->testGetCollectionAs(ClubRole::supervisor);
    if ($this->getCollectionGrantedAccess()[ClubRole::supervisor->value]) {
      $this->assertCount($this->TOTAL_SUPERVISOR_CLUB_1, $response->toArray()['member']);
    }
    return $response;
  }

  public function testGetCollectionAsMemberClub1(): ResponseInterface {
    $this->loggedAsMemberClub1();
    $response = $this->testGetCollectionAs(ClubRole::member);
    if ($this->getCollectionGrantedAccess()[ClubRole::member->value]) {
      $this->assertCount($this->TOTAL_MEMBER_CLUB_1, $response->toArray()['member']);
    }
    return $response;
  }
}
