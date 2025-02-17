<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\ClubDependent\Member;
use App\Entity\ClubDependent\Plugin\Sale\Sale;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsEntityListener(entity: Sale::class)]
class SaleSubscriber extends AbstractEventSubscriber {
  public function __construct(
    private readonly TokenStorageInterface $tokenStorage,
  ) {
  }

  public function prePersist(Sale $sale, PrePersistEventArgs $args): void {
    // We auto set to current logged user
    $member = $this->tokenStorage->getToken()?->getUser();
    if (!$sale->getSeller() && $member instanceof Member) {
      $sale->setSeller($member);
    }

    $this->autoSetFields($sale, $args);
  }

  public function postPersist(Sale $sale, PostPersistEventArgs $args): void {
    $objectManager = $args->getObjectManager();

    // We update the inventory item remaining stock
    foreach ($sale->getSalePurchasedItems() as $purchasedItem) {
      $inventoryItem = $purchasedItem->getItem();
      if (!$inventoryItem || is_null($inventoryItem->getQuantity())) {
        continue;
      }
      $inventoryItem->setQuantity($inventoryItem->getQuantity() - ($purchasedItem->getQuantity() * $inventoryItem->getSellingQuantity()));

      // No more in stock we don't go negative
      if ($inventoryItem->getQuantity() < 0) {
        $inventoryItem->setQuantity(0);
      }

      $objectManager->persist($inventoryItem);
    }
    $objectManager->flush();
  }

  public function preUpdate(Sale $sale, PreUpdateEventArgs $args): void {
    $this->autoSetFields($sale, $args);
  }

  public function postRemove(Sale $sale, PostRemoveEventArgs $args): void {
    // A sale is canceled, we restock the items
    $objectManager = $args->getObjectManager();
    foreach ($sale->getSalePurchasedItems() as $purchasedItem) {
      $inventoryItem = $purchasedItem->getItem();
      if (!$inventoryItem || is_null($inventoryItem->getQuantity())) {
        continue;
      }

      $inventoryItem->setQuantity($inventoryItem->getQuantity() + ($purchasedItem->getQuantity() * $inventoryItem->getSellingQuantity()));
      $objectManager->persist($inventoryItem);
    }
    $objectManager->flush();
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
      $sellingPrice = $salePurchasedItem->getItem()?->getSellingPrice();
      if (!$sellingPrice) { // Selling price still not set at this point, we remove the item
        $sale->removeSalePurchasedItem($salePurchasedItem);
        continue;
      }

      $totalPrice += ($sellingPrice * $salePurchasedItem->getQuantity());
    }

    $sale->setPrice($totalPrice);
  }
}
