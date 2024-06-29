<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\InventoryCategory;
use App\Repository\InventoryCategoryRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;

#[AsEntityListener(entity: InventoryCategory::class)]
class InventoryCategorySubscriber extends AbstractEventSubscriber {
  public function __construct(
    private readonly InventoryCategoryRepository $inventoryCategoryRepository,
  ) {
  }

  public function prePersist(InventoryCategory $inventoryCategory, PrePersistEventArgs $args): void {
    // We auto set the weight to the last one
    if (!$inventoryCategory->getWeight()) {
      $inventoryCategory->setWeight($this->inventoryCategoryRepository->getLatestAvailableWeight());
    }
  }
}
