<?php

namespace App\Repository\Trait;

trait WeightRepositoryTrait {
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

  public function getUpperItem(int $weight): ?object {
    $query = $this->createQueryBuilder('i')
                  ->orderBy('i.weight', "ASC")
                  ->andWhere('i.weight > :weight')
                  ->setParameter('weight', $weight)
                  ->setMaxResults(1)
                  ->getQuery();

    return $query->getOneOrNullResult();
  }

  public function getLowerItem(int $weight): ?object {
    $query = $this->createQueryBuilder('i')
                  ->orderBy('i.weight', "DESC")
                  ->andWhere('i.weight < :weight')
                  ->setParameter('weight', $weight)
                  ->setMaxResults(1)
                  ->getQuery();

    return $query->getOneOrNullResult();
  }
}
