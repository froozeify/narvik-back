<?php

namespace App\Security\Voter;

use ApiPlatform\Metadata\GetCollection;
use App\Entity\Club;
use App\Entity\Interface\ClubLinkedEntityInterface;
use App\Entity\Member;
use App\Entity\Sale;
use App\Entity\User;
use App\Entity\UserMember;
use App\Enum\ClubRole;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ClubVoter extends Voter {

  public function __construct(
    private readonly Security $security,
  ) {
  }

  protected function supports(string $attribute, mixed $subject): bool {
    $roles = [];
    foreach (ClubRole::cases() as $role) {
      $roles[] = $role->value;
    }
    return ($subject instanceof ClubLinkedEntityInterface || $subject instanceof Club) && in_array($attribute, $roles);
  }

  protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
    $user = $token->getUser();
    $targetedClubRole = ClubRole::tryFrom($attribute);

    if (!$user instanceof User || !$targetedClubRole) {
      return false;
    }

    // Super admin have full right
    if ($this->security->isGranted(UserRole::super_admin->value)) {
      return true;
    }

    /** @var Club|null $targetedClub */
    $targetedClub = null;
    if ($subject instanceof Club) {
      $targetedClub = $subject;
    }
    if ($subject instanceof ClubLinkedEntityInterface) {
      $targetedClub = $subject->getClub();
    }

    // No matching club, we denied by default
    if (!$targetedClub) {
      return false;
    }

    foreach ($user->getClubs() as $club) {
      if ($club['club']->getId() === $targetedClub->getId()) {
        /** @var ClubRole $role */
        $role = $club['role'];
        return $role->hasRole($targetedClubRole);
      }
    }

    return false;
  }
}
