<?php

namespace App\Repository\Trait;

use App\Entity\Club;

trait ClubLinkedTrait {
  public function findAllByClub(Club $club): array {
    $qb = $this->createQueryBuilder('e');
    $query = $qb
      ->andWhere("e.club = :club")
      ->setParameter('club', $club)
      ->getQuery();

    return $query->getResult();
  }
}
