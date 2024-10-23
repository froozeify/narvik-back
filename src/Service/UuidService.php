<?php

namespace App\Service;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidService {
  public static function generateUuid(): UuidInterface {
    return UUID::uuid7();
  }
}
