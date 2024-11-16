<?php

namespace App\Repository;

use App\DQL\CustomExpr;
use App\Entity\ClubDependent\Member;
use App\Entity\MemberPresence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Member>
 *
 * @implements PasswordUpgraderInterface<Member>
 *
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends ServiceEntityRepository {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, Member::class);
  }

  /**
   * Will return matching member based on the input string
   * Will be excluded user that don't have a licence number set (i.e: badger, default admin)
   *
   * @param string $string
   * @return array
   */
  public function findByLicenceOrName(string $string): array {
    $string = trim($string);

    $qb = $this->createQueryBuilder("m");
    $qb
      ->andWhere(
        $qb->expr()->orX(
          $qb->expr()->like('m.licence', ':licence'),
          $qb->expr()->like(CustomExpr::unaccentInsensitive('m.firstname'), CustomExpr::unaccentInsensitive(':name')),
          $qb->expr()->like(CustomExpr::unaccentInsensitive('m.lastname'), CustomExpr::unaccentInsensitive(':name')),
          $qb->expr()->like(CustomExpr::unaccentInsensitive($qb->expr()->concat('m.lastname ', 'm.firstname')), CustomExpr::unaccentInsensitive(':name')),
          $qb->expr()->like(CustomExpr::unaccentInsensitive($qb->expr()->concat('m.firstname ', 'm.lastname')), CustomExpr::unaccentInsensitive(':name')),
        ),
      );
    $qb->andWhere(
      $qb->expr()->isNotNull('m.licence'),
    );

    $matches = [];
    preg_match("/^(\d{8,})/m", $string, $matches);

    if (!empty($matches)) {
      $qb->setParameter('licence', $matches[1]);
    } else {
      $qb->setParameter('licence', $string . '%');
    }

    $qb
      ->setParameter('name', '%' . str_replace(' ', '%', $string) . '%')
      ->setMaxResults(10);

    return $qb->getQuery()->getResult();
  }

  public function findOneByLicence(string $licence): ?Member {
    return $this->createQueryBuilder('m')
      ->andWhere('m.licence = :licence')
      ->setParameter('licence', $licence)
      ->setMaxResults(1)
      ->getQuery()
      ->getOneOrNullResult();
  }

  public function findOneByEmail(string $email): ?Member {
    return $this->createQueryBuilder('m')
      ->andWhere('m.email = :email')
      ->setParameter('email', $email)
      ->setMaxResults(1)
      ->getQuery()
      ->getOneOrNullResult();
  }

  public function findAllNotPresentToday(): array {
    $qb = $this->createQueryBuilder('u');

    $memberAlreadyPresents = [];
    $presents = $this->getEntityManager()->getRepository(MemberPresence::class)->findAllPresentToday();
    foreach ($presents as $p) {
      $memberAlreadyPresents[] = $p->getMember()->getId();
    }

    if (!empty($memberAlreadyPresents)) {
      $qb->andWhere(
        $qb->expr()->notIn('u.id', ':presentUsers')
      );
      $qb->setParameter('presentUsers', $memberAlreadyPresents);
    }

    $qb
      ->addOrderBy('u.lastname', 'ASC')
      ->addOrderBy('u.firstname', 'ASC');

    return $qb->getQuery()->getResult();
  }

  public function countTotalMembers(): int {
    $qb = $this->createQueryBuilder("m");
    return $qb
      ->select($qb->expr()->count("m.id"))
      ->andWhere($qb->expr()->isNotNull("m.licence"))
      ->getQuery()->getSingleScalarResult();
  }
}
