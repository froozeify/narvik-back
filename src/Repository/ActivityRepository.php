<?php

namespace App\Repository;

use App\Controller\ActivityMergeTo;
use App\Entity\Activity;
use App\Entity\ExternalPresence;
use App\Entity\MemberPresence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Activity>
 *
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, Activity::class);
  }

  public function findByIds(array $activities): array {
    $qb = $this->createQueryBuilder('a');
    return $qb
      ->andWhere($qb->expr()->in('a.id', ':activities'))
      ->setParameter('activities', $activities)
      ->getQuery()
      ->getResult();
  }

  public function mergeAndDelete(Activity $activityToMerge, Activity $targetActivity): void {
    // We update all the member presences
    $mps = $this->getEntityManager()->getRepository(MemberPresence::class)->findAllByActivity($activityToMerge);
    /** @var MemberPresence $mp */
    foreach ($mps as $mp) {
      $mp->removeActivity($activityToMerge);
      $mp->addActivity($targetActivity);
      $this->getEntityManager()->persist($mp);
    }

    // We update all the external presences
    $eps = $this->getEntityManager()->getRepository(ExternalPresence::class)->findAllByActivity($activityToMerge);
    /** @var ExternalPresence $ep */
    foreach ($eps as $ep) {
      $ep->removeActivity($activityToMerge);
      $ep->addActivity($targetActivity);
      $this->getEntityManager()->persist($ep);
    }

    $this->getEntityManager()->remove($activityToMerge);

    $this->getEntityManager()->flush();
  }

//    /**
//     * @return Activity[] Returns an array of Activity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Activity
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
