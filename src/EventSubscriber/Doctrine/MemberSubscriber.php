<?php

namespace App\EventSubscriber\Doctrine;

use App\Entity\Member;
use App\Enum\GlobalSetting;
use App\Repository\ActivityRepository;
use App\Repository\MemberPresenceRepository;
use App\Repository\MemberRepository;
use App\Service\GlobalSettingService;
use App\Service\MemberPhotoService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(entity: Member::class)]
class MemberSubscriber extends AbstractEventSubscriber {
  public function __construct(
    private UserPasswordHasherInterface $passwordHasher,
    private MemberPhotoService $memberPhotoService,
    private GlobalSettingService $globalSettingService,
    private ActivityRepository $activityRepository,
    private MemberPresenceRepository $memberPresenceRepository,
  ) {
  }

  public function prePersist(Member $member, PrePersistEventArgs $args): void {
    $this->updatePassword($member);
  }

  public function postLoad(Member $member, PostLoadEventArgs $args): void {
    if ($member->getLicence() && $photoPath = $this->memberPhotoService->getMemberPhotoPath($member->getLicence())) {
      $member->setProfileImage($photoPath);
    }

    $controlShootingActivity = $this->globalSettingService->getSettingValue(GlobalSetting::CONTROL_SHOOTING_ACTIVITY_ID);
    if ($controlShootingActivity) {
      $controlShootingActivity = $this->activityRepository->find($controlShootingActivity);
      if ($controlShootingActivity) {
        $presence = $this->memberPresenceRepository->findLastOneByActivity($member, $controlShootingActivity);
        if ($presence) {
          $member->setLastControlShooting($presence->getDate());
        }
      }
    }
  }

  private function updatePassword(Member $member): void {
    if (!empty($member->getPlainPassword())) {
      $member->setPassword($this->passwordHasher->hashPassword($member, $member->getPlainPassword()));
    }
  }
}
