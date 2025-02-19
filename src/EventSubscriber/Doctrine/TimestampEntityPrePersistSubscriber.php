<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\Interface\TimestampEntityInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
class TimestampEntityPrePersistSubscriber extends AbstractEventSubscriber {
  public function prePersist(PrePersistEventArgs $args): void {
    $entity = $args->getObject();
    if (!$entity instanceof TimestampEntityInterface) {
      return;
    }

    if (!$entity->getCreatedAt()) {
      $entity->setCreatedAt(new \DateTimeImmutable());
    }

    if (!$entity->getUpdatedAt()) {
      $entity->setUpdatedAt(new \DateTimeImmutable());
    }
  }
}
