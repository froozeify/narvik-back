<?php

namespace App\Service;

use App\Entity\Club;
use App\Entity\User;
use App\Entity\UserMember;
use App\Enum\ClubRole;
use App\Enum\UserRole;
use App\Repository\ClubRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class ClubService {
  public function __construct(
    private EntityManagerInterface $entityManager,
    private UserRepository $userRepository,
  ) {

  }


  /**
   * Automatically create the Badger user if not present.
   * Otherwise, just return it.
   *
   * It will check that the club has a badgerToken defined
   *
   * @param Club $club
   *
   * @return User|null
   */
  public function getBadger(Club $club): ?User {
    if (empty($club->getBadgerToken())) {
      return null;
    }

    // We try finding it
    $user = $this->userRepository->loadUserByIdentifier("badger@{$club->getUuid()->toString()}");
    if (!$user) {
      $user = new User();
      $user
        ->setAccountActivated(true)
        ->setFirstname('Badger')
        ->setLastname('BADGER')
        ->setRole(UserRole::badger)
        ->setEmail("badger@{$club->getUuid()->toString()}");

      // We link the user to the club
      $userMember = new UserMember();
      $userMember
        ->setUser($user)
        ->setBadgerClub($club)
        ->setRole(ClubRole::badger);

      $this->entityManager->persist($user);
      $this->entityManager->persist($userMember);
      $this->entityManager->flush();
      // We refresh so getLinkedClubs() contain the club
      $this->entityManager->refresh($user);
    }

    // We check the club are matching
    $matched = false;
    foreach ($user->getLinkedClubs() as $dbClub) {
      if ($dbClub['club'] === $club) {
        $matched = true;
        break;
      }
    }

    if (!$matched) {
      return null;
    }

    return $user;
  }

}
