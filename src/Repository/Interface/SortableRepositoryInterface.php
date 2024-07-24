<?php

namespace App\Repository\Interface;

use App\Entity\Interface\SortableEntityInterface;

interface SortableRepositoryInterface {
  public function getLatestAvailableWeight(): int;
  public function getUpperItem(int $currentWeight): ?SortableEntityInterface;
  public function getLowerItem(int $currentWeight): ?SortableEntityInterface;

  public function moveUp(SortableEntityInterface $itemToMove): void;
  public function moveDown(SortableEntityInterface $itemToMove): void;
}
