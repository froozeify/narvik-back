<?php

namespace App\Controller\ClubDependent\Plugin\Presence;

use App\Enum\GlobalSetting;
use App\Repository\MemberPresenceRepository;
use App\Service\GlobalSettingService;
use App\Service\MemberService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MemberPresenceToday extends AbstractController {

  public function __invoke(MemberPresenceRepository $memberPresenceRepository, MemberService $memberService, GlobalSettingService $globalSettingService): ?array {
    $todayPresentMembers = $memberPresenceRepository->findAllPresentToday();

    $controlShootingActivity = $globalSettingService->getSettingValue(GlobalSetting::CONTROL_SHOOTING_ACTIVITY_ID);

    foreach ($todayPresentMembers as $memberPresence) {
      $memberService->setCurrentSeason($memberPresence->getMember());
      if ($controlShootingActivity && is_numeric($controlShootingActivity)) {
        $memberService->setLastControlShooting($memberPresence->getMember());
      }
    }

    return $todayPresentMembers;
  }

}
