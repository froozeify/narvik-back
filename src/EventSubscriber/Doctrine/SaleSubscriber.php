<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\Sale;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(entity: Sale::class)]
class SaleSubscriber extends AbstractEventSubscriber {
  public function __construct() {
  }

  public function prePersist(Sale $sale, PrePersistEventArgs $args): void {
    $this->autoSetFields($sale, $args);
  }

  public function preUpdate(Sale $sale, PreUpdateEventArgs $args): void {
    $this->autoSetFields($sale, $args);
  }

  public function autoSetFields(Sale $sale, LifecycleEventArgs $args): void {
    if ($sale->getPrice()) {
      return;
    }

    $totalPrice = 0;
    foreach ($sale->getSalePurchasedItems() as $salePurchasedItem) {
      if ($salePurchasedItem->getItemPrice()) {
        $totalPrice += ($salePurchasedItem->getItemPrice() * $salePurchasedItem->getQuantity());
        continue;
      }

      // Item price is not set we get it for the InventoryItem object
      if (!$salePurchasedItem->getItem()) {
        continue;
      }

      $sellingPrice = $salePurchasedItem->getItem()->getSellingPrice();
      if (!$sellingPrice) { // Selling price still not set at this point, we remove the item
        $sale->removeSalePurchasedItem($salePurchasedItem);
        continue;
      }

      $totalPrice += ($sellingPrice * $salePurchasedItem->getQuantity());
    }

    $sale->setPrice($totalPrice);
  }
}
