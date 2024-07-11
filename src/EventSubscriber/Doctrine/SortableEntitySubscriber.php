<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\Interface\SortableEntityInterface;
use App\Repository\Interface\SortableRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
class SortableEntitySubscriber extends AbstractEventSubscriber {
  public function prePersist(PrePersistEventArgs $args): void {
    $entity = $args->getObject();
    if (!$entity instanceof SortableEntityInterface) {
      return;
    }

    $repository = $args->getObjectManager()->getRepository(get_class($entity));
    if (!$repository instanceof SortableRepositoryInterface) {
      return;
    }

    // We auto set the weight to the last one
    if (!$entity->getWeight()) {
      $entity->setWeight($repository->getLatestAvailableWeight());
    }
  }
}
