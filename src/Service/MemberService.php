<?php

namespace App\Service;

use App\Entity\ClubDependent\Member;
use App\Enum\GlobalSetting;
use App\Repository\ActivityRepository;
use App\Repository\MemberPresenceRepository;
use App\Repository\SeasonRepository;

class MemberService {
  public function __construct(
    private readonly ImageService $imageService,
    private readonly GlobalSettingService $globalSettingService,
    private readonly ActivityRepository $activityRepository,
    private readonly MemberPresenceRepository $memberPresenceRepository,
    private readonly SeasonRepository $seasonRepository,
  ) {
  }

  public function setProfileImage(Member $member): void {
    if ($member->getLicence() && $photoPath = $this->imageService->getMemberPhotoPath($member->getLicence())) {
      $member->setProfileImage($photoPath);
    }
  }

  public function setCurrentSeason(Member $member): void {
    $currentSeason = $this->seasonRepository->findCurrentSeason();
    if (!$currentSeason) return;

    foreach ($member->getMemberSeasons() as $season) {
      if ($season->getSeason() === $currentSeason) {
        $member->setCurrentSeason($season);
        break;
      }
    }
  }

  public function setLastControlShooting(Member $member, ?string $controlShootingActivity = null): void {
    if (!$controlShootingActivity) {
      $controlShootingActivity = $this->globalSettingService->getSettingValue(GlobalSetting::CONTROL_SHOOTING_ACTIVITY_ID);
    }

    // No control shooting defined
    if (!$controlShootingActivity || !is_numeric($controlShootingActivity)) {
      return;
    }

    $controlShootingActivity = $this->activityRepository->find($controlShootingActivity);
    if (!$controlShootingActivity) { // Control shooting id passed does not exist
      return;
    }

    $presence = $this->memberPresenceRepository->findLastOneByActivity($member, $controlShootingActivity);
    if ($presence) {
      $member->setLastControlShooting($presence->getDate());
    }
  }
}
