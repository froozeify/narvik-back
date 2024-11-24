<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use App\Doctrine\UserClubExtension;
use App\Entity\Club;
use App\Repository\Trait\UuidEntityRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Club>
 */
class ClubRepository extends ServiceEntityRepository {
  use UuidEntityRepositoryTrait;

  public function __construct(
    ManagerRegistry $registry,
    private readonly UserClubExtension $userClubExtension,
  ) {
    parent::__construct($registry, Club::class);
  }

  public function findOneByUuidRestrained(string $uuid): ?Club {
    $qb = $this->createQueryBuilder('e')
                  ->andWhere('e.uuid = :uuid')
                  ->setParameter('uuid', $uuid)
                  ->setMaxResults(1)
                  ;
    $this->userClubExtension->applyToItem($qb, new QueryNameGenerator(), Club::class, ['uuid' => $uuid], context: ['_source' => 'club_repository']);
    $query = $qb->getQuery();

    try {
      return $query->getOneOrNullResult();
    }
    catch (\Exception $e) {
      return null;
    }
  }
}
