<?php

namespace App\Security\Voter;

use App\Entity\Member;
use App\Entity\Sale;
use App\Enum\MemberRole;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SaleVoter extends Voter {
  public const UPDATE = 'SALE_UPDATE';
  public const DELETE = 'SALE_DELETE';

  public function __construct(
    private readonly Security $security
  ) {
  }

  protected function supports(string $attribute, mixed $subject): bool {
    return $subject instanceof Sale && in_array($attribute, [self::UPDATE, self::DELETE]);
  }

  protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
    $user = $token->getUser();

    // Not a member
    if (
      !$user instanceof Member ||
      !$subject instanceof Sale ||
      !$this->security->isGranted(MemberRole::supervisor->value)
    ) {
      return false;
    }

    return $subject->getCreatedAt() >= new \DateTimeImmutable('today midnight');
  }
}
