<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\Interface\TimestampEntityInterface;
use App\Entity\Interface\UuidEntityInterface;
use App\Service\UuidService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
class UuidPrePersistSubscriber extends AbstractEventSubscriber {
  public function prePersist(PrePersistEventArgs $args): void {
    $entity = $args->getObject();
    if (!$entity instanceof UuidEntityInterface) {
      return;
    }

    // We auto set the weight to the last one
    if (!$entity->getUuid()) {
      $entity->setUuid(UuidService::generateUuid());
    }
  }
}
