<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\Sale;
use App\Entity\SalePurchasedItem;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(entity: SalePurchasedItem::class)]
class SalePurchasedItemSubscriber extends AbstractEventSubscriber {
  public function __construct() {
  }

  public function prePersist(SalePurchasedItem $salePurchasedItem, PrePersistEventArgs $args): void {
    $this->autoSetFields($salePurchasedItem, $args);
  }

  public function preUpdate(SalePurchasedItem $salePurchasedItem, PreUpdateEventArgs $args): void {
    $this->autoSetFields($salePurchasedItem, $args);
  }

  public function autoSetFields(SalePurchasedItem $salePurchasedItem, LifecycleEventArgs $args): void {
    $item = $salePurchasedItem->getItem();
    if (!$item) {
      return;
    }

    if (!$salePurchasedItem->getItemPrice()) {
      $salePurchasedItem->setItemPrice($item->getSellingPrice());
    }

    // We always update the itemName && category
    $salePurchasedItem
      ->setItemName($item->getName())
      ->setItemCategory($item->getCategory()?->getName());
  }
}
