<?php

namespace App\Repository\ClubDependent;

use App\Entity\ClubDependent\Member;
use App\Entity\ClubDependent\MemberSeason;
use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MemberSeason>
 *
 * @method MemberSeason|null find($id, $lockMode = null, $lockVersion = null)
 * @method MemberSeason|null findOneBy(array $criteria, array $orderBy = null)
 * @method MemberSeason[]    findAll()
 * @method MemberSeason[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberSeasonRepository extends ServiceEntityRepository {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, MemberSeason::class);
  }

  public function countTotalMembersForSeason(Season $season): int {
    $qb = $this->createQueryBuilder("m");
    return $qb
      ->select($qb->expr()->count("m.id"))
      ->andWhere("m.season = :season")
      ->setParameter("season", $season)
      ->getQuery()->getSingleScalarResult();
  }

  public function countTotalMembersForThisSeason(): int {
    $currentSeason = $this->getEntityManager()->getRepository(Season::class)->findCurrentSeason();
    if (!$currentSeason) return 0;
    return $this->countTotalMembersForSeason($currentSeason);
  }

  public function countTotalMembersForPreviousSeason(): int {
    $currentSeason = $this->getEntityManager()->getRepository(Season::class)->findPreviousSeason();
    if (!$currentSeason) return 0;
    return $this->countTotalMembersForSeason($currentSeason);
  }

  public function findOneByMemberAndSeason(Member $member, Season $season): ?MemberSeason {
    $qb = $this->createQueryBuilder('m')
               ->andWhere('m.member = :member')
               ->andWhere('m.season = :season')
               ->setParameter('member', $member)
               ->setParameter('season', $season);

    try {
      return $qb->getQuery()->getOneOrNullResult();
    }
    catch (\Exception) {
      return null;
    }
  }

//    /**
//     * @return MemberSeason[] Returns an array of MemberSeason objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
}
