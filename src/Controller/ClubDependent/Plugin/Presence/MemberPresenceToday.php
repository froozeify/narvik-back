<?php

namespace App\Controller\ClubDependent\Plugin\Presence;

use App\Controller\Abstract\AbstractClubDependentController;
use App\Repository\MemberPresenceRepository;
use App\Service\GlobalSettingService;
use App\Service\MemberService;

class MemberPresenceToday extends AbstractClubDependentController {

  public function __invoke(MemberPresenceRepository $memberPresenceRepository, MemberService $memberService, GlobalSettingService $globalSettingService): ?array {

    $todayPresentMembers = $memberPresenceRepository->findAllPresentToday($this->getQueryClub());

    $controlShootingActivity = $this->getQueryClub()->getSettings()?->getControlShootingActivity();

    foreach ($todayPresentMembers as $memberPresence) {
      $memberService->setCurrentSeason($memberPresence->getMember());
      if ($controlShootingActivity) {
        $memberService->setLastControlShooting($memberPresence->getMember(), $controlShootingActivity);
      }
    }

    return $todayPresentMembers;
  }

}
