<?php

namespace App\Repository\Trait;

use App\Entity\Club;
use App\Entity\Interface\UuidEntityInterface;
use Doctrine\ORM\QueryBuilder;

trait ClubLinkedTrait {
  public function applyClubRestriction(QueryBuilder $qb, Club $club): QueryBuilder {
    $alias = $qb->getRootAliases()[0];
    return $qb
      ->andWhere($qb->expr()->eq($alias . '.' . $this->getClassName()::getClubSqlPath(), ':club'))
      ->setParameter('club', $club);
  }

  public function findAllByClub(Club $club): array {
    $qb = $this->createQueryBuilder('e');
    $this->applyClubRestriction($qb, $club);
    $query = $qb->getQuery();
    return $query->getResult();
  }

  public function findOneByClubAndUuid(Club $club, string $uuid): ?UuidEntityInterface {
    $qb = $this->createQueryBuilder('e');
    $this->applyClubRestriction($qb, $club);
    $query = $qb
      ->andWhere('e.uuid = :uuid')
      ->setParameter('uuid', $uuid)
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
