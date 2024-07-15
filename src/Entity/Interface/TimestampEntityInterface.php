<?php

namespace App\Entity\Interface;

interface TimestampEntityInterface {

  public function getCreatedAt(): ?\DateTimeImmutable;
  public function setCreatedAt(\DateTimeImmutable $date): static;

  public function getUpdatedAt(): ?\DateTimeImmutable;
  public function setUpdatedAt(\DateTimeImmutable $date): static;
}
