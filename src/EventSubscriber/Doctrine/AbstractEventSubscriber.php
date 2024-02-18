<?php

namespace App\EventSubscriber\Doctrine;

use Doctrine\Persistence\ObjectManager;

abstract class AbstractEventSubscriber {

  protected function getChangedProperties(ObjectManager $objectManager, $entity): array {
    $changedProperties = $objectManager->getUnitOfWork()->getEntityChangeSet($entity);
    unset($changedProperties["createdAt"]);
    unset($changedProperties["updatedAt"]);
    return $changedProperties;
  }

  protected function isPropertyChanged(ObjectManager $objectManager, $entity, $property): bool {
    return array_key_exists($property, $this->getChangedProperties($objectManager, $entity));
  }

  /**
   * Force to have $properties and only them changed at the same time (all must be present and changed)
   * Maybe a rework with a more gentle method could be wanted
   *
   * @param mixed $item
   * @param array $properties
   * @return bool
   * @see hasOnlyWhitelistedChangedProperties for a more gentle method
   *
   */
  protected function hasOnlyChangedProperties(ObjectManager $objectManager, $entity, array $properties): bool {
    if (count($this->getChangedProperties($objectManager, $entity)) !== count($properties)) return false;
    foreach ($properties as $property) {
      if (!$this->isPropertyChanged($objectManager, $entity, $property)) return false;
    }
    return true;
  }

  /**
   * The changed elements must be part of the allowedProperties, not all elements must be present
   *
   * @param mixed $item
   * @param array $allowedProperties
   * @return bool
   */
  protected function hasOnlyWhitelistedChangedProperties(ObjectManager $objectManager, $entity, array $allowedProperties): bool {
    $changedProps = $this->getChangedProperties($objectManager, $entity);
    foreach (array_keys($changedProps) as $changedProp) {
      if (!in_array($changedProp, $allowedProperties)) {
        return false;
      }
    }
    return true;
  }
}
