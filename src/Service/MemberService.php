<?php

namespace App\Service;

use App\Entity\ClubDependent\Member;
use App\Entity\ClubDependent\Plugin\Presence\Activity;
use App\Repository\ClubDependent\Plugin\Presence\ActivityRepository;
use App\Repository\ClubDependent\Plugin\Presence\MemberPresenceRepository;
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

  public function setLastControlShooting(Member $member, ?Activity $controlShootingActivity = null): void {
    if (!$controlShootingActivity) {
      return;
    }

    $presence = $this->memberPresenceRepository->findLastOneByActivity($member, $controlShootingActivity);
    if ($presence) {
      $member->setLastControlShooting($presence->getDate());
    }
  }
}
