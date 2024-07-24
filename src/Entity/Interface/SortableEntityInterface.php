<?php

namespace App\Entity\Interface;

interface SortableEntityInterface {

  public function getWeight(): ?int;
  public function setWeight(int $weight): static;
}
