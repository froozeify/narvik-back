<?php

namespace App\Repository\Trait;

use App\Entity\Interface\SortableEntityInterface;

trait SortableEntityRepositoryTrait {
  public function getLatestAvailableWeight(): int {
    $query = $this->createQueryBuilder('i')
                  ->orderBy('i.weight', "DESC")
                  ->setMaxResults(1)
                  ->getQuery();

    $lastWeight = null;
    try {
      $result = $query->getOneOrNullResult();
      if ($result) {
        $lastWeight = $result->getWeight();
      }
    } catch (\Exception $e) {
    }

    if (!$lastWeight) {
      $lastWeight = 0;
    }

    return $lastWeight + 1;
  }

  public function getUpperItem(int $currentWeight): ?SortableEntityInterface {
    $query = $this->createQueryBuilder('i')
                  ->orderBy('i.weight', "DESC")
                  ->andWhere('i.weight < :weight')
                  ->setParameter('weight', $currentWeight)
                  ->setMaxResults(1)
                  ->getQuery();

    return $query->getOneOrNullResult();
  }

  public function getLowerItem(int $currentWeight): ?SortableEntityInterface {
    $query = $this->createQueryBuilder('i')
                  ->orderBy('i.weight', "ASC")
                  ->andWhere('i.weight > :weight')
                  ->setParameter('weight', $currentWeight)
                  ->setMaxResults(1)
                  ->getQuery();

    return $query->getOneOrNullResult();
  }

  public function moveUp(SortableEntityInterface $itemToMove): void {
    // We store the old weight since we make a swap
    $itemWeight = $itemToMove->getWeight();

    $targetItem = $this->getUpperItem($itemWeight);
    dump(
      'target',
      $targetItem
    );
    // No item found, we do nothing
    if (!$targetItem) {
      return;
    }

    // We swap the weights
    $itemToMove->setWeight($targetItem->getWeight());
    $targetItem->setWeight($itemWeight);

    $this->getEntityManager()->persist($itemToMove);
    $this->getEntityManager()->persist($targetItem);
    $this->getEntityManager()->flush();
  }

  public function moveDown(SortableEntityInterface $itemToMove): void {
    // We store the old weight since we make a swap
    $itemWeight = $itemToMove->getWeight();

    $targetItem = $this->getLowerItem($itemWeight);
    // No item found, we do nothing
    if (!$targetItem) {
      return;
    }

    // We swap the weights
    $itemToMove->setWeight($targetItem->getWeight());
    $targetItem->setWeight($itemWeight);

    $this->getEntityManager()->persist($itemToMove);
    $this->getEntityManager()->persist($targetItem);
    $this->getEntityManager()->flush();
  }
}
