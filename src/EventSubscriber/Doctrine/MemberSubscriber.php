<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\ClubDependent\Member;
use App\Service\MemberService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;

#[AsEntityListener(entity: Member::class)]
class MemberSubscriber extends AbstractEventSubscriber {
  public function __construct(
    private readonly MemberService $memberService,
  ) {
  }

  public function postLoad(Member $member, PostLoadEventArgs $args): void {
    $this->memberService->setCurrentSeason($member);

    $controlShootingActivity = $member->getClub()?->getSettings()?->getControlShootingActivity();
    $this->memberService->setLastControlShooting($member, $controlShootingActivity);
  }

  public function postPersist(Member $member, PostPersistEventArgs $args): void {
    $this->memberService->autolinkMemberWithUser($member);
  }
}
