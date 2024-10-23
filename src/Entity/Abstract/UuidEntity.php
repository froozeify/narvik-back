<?php

namespace App\Entity\Abstract;

use App\Entity\Interface\UuidEntityInterface;
use App\Entity\Trait\UuidTrait;
use App\Service\UuidService;

abstract class UuidEntity implements UuidEntityInterface {
  use UuidTrait;

  public function __construct() {
    $this->uuid = UuidService::generateUuid();
  }
}
