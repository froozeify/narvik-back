<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\Member;
use App\Service\MemberService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(entity: Member::class)]
class MemberSubscriber extends AbstractEventSubscriber {
  public function __construct(
    private readonly MemberService $memberService,
  ) {
  }

  public function prePersist(Member $member, PrePersistEventArgs $args): void {
    $this->updatePassword($member);
  }

  public function postLoad(Member $member, PostLoadEventArgs $args): void {
    $this->memberService->setProfileImage($member);
    $this->memberService->setCurrentSeason($member);
    $this->memberService->setLastControlShooting($member);
  }

  private function updatePassword(Member $member): void {
    if (!empty($member->getPlainPassword())) {
      $changeError = $this->memberService->changeMemberPassword($member, $member->getPlainPassword(), false);
      if ($changeError) {
        throw new HttpException(Response::HTTP_BAD_REQUEST, $changeError);
      }
    }
  }
}
