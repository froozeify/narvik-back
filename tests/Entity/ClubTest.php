<?php

namespace App\Tests\Entity;

use App\Entity\Club;

class ClubTest extends AbstractEntityTestCase {

  protected function getClassname(): string {
    return Club::class;
  }

  protected function getRootUri(): string {
    return '/clubs';
  }

  // TODO: Create test to check only superadmin and club_admin can see the `badgerToken` field
}
