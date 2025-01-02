<?php

namespace App\Repository;

use App\Entity\ClubDependent\Plugin\Presence\ExternalPresence;
use App\Repository\Interface\PresenceRepositoryInterface;
use App\Repository\Trait\PresenceRepositoryTrait;
use App\Service\GlobalSettingService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExternalPresence>
 *
 * @method ExternalPresence|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExternalPresence|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExternalPresence[]    findAll()
 * @method ExternalPresence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExternalPresenceRepository extends ServiceEntityRepository implements PresenceRepositoryInterface {
  use PresenceRepositoryTrait;

  private GlobalSettingService $globalSettingService;

  public function __construct(ManagerRegistry $registry, GlobalSettingService $globalSettingService) {
    parent::__construct($registry, ExternalPresence::class);
    $this->globalSettingService = $globalSettingService;
  }

  public function findAllWithLicence(): ?array {
    $qb = $this->createQueryBuilder('e');
    return $qb
      ->where($qb->expr()->isNotNull("e.licence"))
      ->orderBy("e.licence", "ASC")
      ->getQuery()->getResult();
  }

}
