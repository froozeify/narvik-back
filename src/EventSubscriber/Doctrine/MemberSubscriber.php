<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\ClubDependent\Member;
use App\Service\MemberService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostLoadEventArgs;

#[AsEntityListener(entity: Member::class)]
class MemberSubscriber extends AbstractEventSubscriber {
  public function __construct(
    private readonly MemberService $memberService,
  ) {
  }

  public function postLoad(Member $member, PostLoadEventArgs $args): void {
    $this->memberService->setProfileImage($member);
    $this->memberService->setCurrentSeason($member);
    $this->memberService->setLastControlShooting($member);
  }
}
