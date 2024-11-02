<?php

namespace App\Tests\Entity;

use App\Enum\ClubRole;
use App\Enum\UserRole;
use App\Tests\AbstractTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class AbstractEntityClubLinkedTestCase extends AbstractEntityTestCase {

  /**
   * Mapping of the collection response code
   *
   * @return array{string, bool}
   */
  protected function getCollectionGrantedAccess() : array {
    $access = parent::getCollectionGrantedAccess();
    $access[ClubRole::admin->value] = true;
    $access[ClubRole::supervisor->value] = true;
    return $access;
  }

  public function testGetCollectionAsClub1Admin(): void {
//    $this->loggedAsSuperAdmin();
//    $this->testGetCollectionAs(UserRole::super_admin);
    return;
  }
}
