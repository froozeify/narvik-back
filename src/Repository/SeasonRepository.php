<?php

namespace App\Repository;

use App\Entity\Season;
use App\Service\UtilsService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Season>
 *
 * @method Season|null find($id, $lockMode = null, $lockVersion = null)
 * @method Season|null findOneBy(array $criteria, array $orderBy = null)
 * @method Season[]    findAll()
 * @method Season[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeasonRepository extends ServiceEntityRepository {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, Season::class);
  }

  public function findOneByName(string $seasonName): ?Season {
    return $this->createQueryBuilder("s")
         ->andWhere("s.name = :seasonName")
         ->setParameter("seasonName", $seasonName)
         ->setMaxResults(1)
         ->getQuery()
         ->getOneOrNullResult();
  }

  public function findCurrentSeason():?Season  {
    return $this->findOneByName(UtilsService::getCurrentSeasonName());
  }

  public function findPreviousSeason():?Season  {
    return $this->findOneByName(UtilsService::getPreviousSeasonName());
  }

//    /**
//     * @return Season[] Returns an array of Season objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Season
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
