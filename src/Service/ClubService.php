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
    private readonly EntityManagerInterface $entityManager,
    private readonly UserRepository $userRepository,
    private readonly ClubRepository $clubRepository,
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
      // We refresh so getLinkedProfiles() contain the club
      $this->entityManager->refresh($user);
    }

    // We check the club are matching
    $matched = false;
    foreach ($user->getLinkedProfiles() as $dbClub) {
      if ($dbClub->getClub() === $club) {
        $matched = true;
        break;
      }
    }

    if (!$matched) {
      return null;
    }

    return $user;
  }

  public function setItacImport(Club $club, int $numberOfBatches): void {
    $clubSettings = $club->getSettings();
    $clubSettings
      ->setItacImportRemaining($numberOfBatches)
      ->setItacImportDate(new \DateTimeImmutable());

    $this->entityManager->persist($clubSettings);
    $this->entityManager->flush();
  }

  public function setItacSecondaryImport(Club $club, int $numberOfBatches): void {
    $clubSettings = $club->getSettings();
    $clubSettings
      ->setItacSecondaryImportRemaining($numberOfBatches)
      ->setItacSecondaryImportDate(new \DateTimeImmutable());

    $this->entityManager->persist($clubSettings);
    $this->entityManager->flush();
  }

  public function setCerbereImport(Club $club, int $numberOfBatches): void {
    $clubSettings = $club->getSettings();
    $clubSettings
      ->setCerbereImportRemaining($numberOfBatches);

    $this->entityManager->persist($clubSettings);
    $this->entityManager->flush();
  }

  public function consumeMessage(string $clubUuid, string $clubSettingRemainingField): void {
    $club = $this->clubRepository->findOneByUuid($clubUuid);
    if (!$club instanceof Club) {
      return;
    }

    $getter = "get" . $clubSettingRemainingField;
    $setter = "set" . $clubSettingRemainingField;

    $clubSettings = $club->getSettings();
    $clubSettings
      ->$setter($clubSettings->$getter() - 1);

    $this->entityManager->persist($clubSettings);
    $this->entityManager->flush();
  }

}
