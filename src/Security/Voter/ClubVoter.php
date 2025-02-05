<?php

namespace App\Security\Voter;

use App\Entity\Club;
use App\Entity\Interface\ClubLinkedEntityInterface;
use App\Entity\User;
use App\Enum\ClubRole;
use App\Enum\UserRole;
use App\Service\RequestService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ClubVoter extends Voter {

  public function __construct(
    private readonly Security $security,
    private readonly RequestStack $requestStack,
    private readonly RequestService $requestService,
  ) {
  }

  protected function supports(string $attribute, mixed $subject): bool {
    $roles = [];
    foreach (ClubRole::cases() as $role) {
      $roles[] = $role->value;
    }

    if ($subject instanceof Request) {
      $subject = $this->requestService->getClubFromRequest($subject, false);
    }

    return ($subject instanceof ClubLinkedEntityInterface || $subject instanceof Club) && in_array($attribute, $roles);
  }

  protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
    $selectedProfile = $this->requestService->getSelectedProfileFromRequest($this->requestStack->getCurrentRequest());
    if ($subject instanceof Request) {
      $selectedProfile = $this->requestService->getSelectedProfileFromRequest($subject);
      $subject = $this->requestService->getClubFromRequest($subject);
    }

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

    $linkedProfiles = $user->getLinkedProfiles();
    // When user have multiple profiles linked, the member header must be specified
    if (!$selectedProfile && count($linkedProfiles) > 1) {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Missing required 'Profile' header.");
    }

    foreach ($linkedProfiles as $linkedProfile) {
      if ($selectedProfile) {
        if (!$linkedProfile['id'] || $linkedProfile['id'] !== $selectedProfile) {
          continue;
        }
      }

      if ($linkedProfile['club']->getId() === $targetedClub->getId()) {
        /** @var ClubRole $role */
        $role = $linkedProfile['role'];
        return $role->hasRole($targetedClubRole);
      }
    }

    return false;
  }
}
