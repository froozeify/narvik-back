<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, User::class);
  }

  /**
   * Used to upgrade (rehash) the user's password automatically over time.
   */
  public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword, bool $flush = true): void {
    if (!$user instanceof User) {
      throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
    }

    $user->setPassword($newHashedPassword);
    $user->setPlainPassword(null);
    if ($flush) {
      $this->getEntityManager()->persist($user);
      $this->getEntityManager()->flush();
    }
  }

  public function loadUserByIdentifier(string $identifier): ?UserInterface {
    return $this->getEntityManager()->getRepository(User::class)->findOneBy([
        'email'            => $identifier,
        'accountActivated' => true,
      ]);
  }

  public function findOneByEmail(string $email): ?User {
    return $this->createQueryBuilder('m')
      ->andWhere('m.email = :email')
      ->setParameter('email', $email)
      ->setMaxResults(1)
      ->getQuery()
      ->getOneOrNullResult();
  }

}
