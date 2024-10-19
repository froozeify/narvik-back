<?php

namespace App\Entity\Interface;

use Ramsey\Uuid\UuidInterface;

interface UuidEntityInterface {

  public function getId(): ?int;
  public function getUuid(): string;
  public function setUuid(UuidInterface $uuid): static;
}
