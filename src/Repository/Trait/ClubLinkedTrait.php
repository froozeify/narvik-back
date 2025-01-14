<?php

namespace App\Repository\Trait;

use App\Entity\Club;
use App\Entity\Interface\UuidEntityInterface;

trait ClubLinkedTrait {
  public function findAllByClub(Club $club): array {
    $qb = $this->createQueryBuilder('e');
    $query = $qb
      ->andWhere($qb->expr()->eq('e.' . $this->getClassName()::getClubSqlPath(), ':club'))
      ->setParameter('club', $club)
      ->getQuery();

    return $query->getResult();
  }

  public function findOneByClubAndUuid(Club $club, string $uuid): ?UuidEntityInterface {
    $qb = $this->createQueryBuilder('e');
    $query = $qb
      ->andWhere('e.uuid = :uuid')
      ->andWhere($qb->expr()->eq('e.' . $this->getClassName()::getClubSqlPath(), ':club'))
      ->setParameter('uuid', $uuid)
      ->setParameter('club', $club)
      ->setMaxResults(1)
      ->getQuery();

    try {
      return $query->getOneOrNullResult();
    }
    catch (\Exception $e) {
      return null;
    }
  }
}
