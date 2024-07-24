<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\Interface\TimestampEntityInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::preUpdate)]
class TimestampEntityPreUpdateSubscriber extends AbstractEventSubscriber {
  public function preUpdate(PreUpdateEventArgs $args): void {
    $entity = $args->getObject();
    if (!$entity instanceof TimestampEntityInterface) {
      return;
    }

   $entity->setUpdatedAt(new \DateTimeImmutable());
  }
}
