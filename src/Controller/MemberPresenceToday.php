<?php

namespace App\Controller;

use App\Enum\GlobalSetting;
use App\Repository\ActivityRepository;
use App\Repository\MemberPresenceRepository;
use App\Service\GlobalSettingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class MemberPresenceToday extends AbstractController {

  public function __invoke(Request $request, MemberPresenceRepository $memberPresenceRepository, ActivityRepository $activityRepository, GlobalSettingService $globalSettingService): ?array {
    $todayPresentMembers = $memberPresenceRepository->findAllPresentToday();

    $controlShootingActivity = $globalSettingService->getSettingValue(GlobalSetting::CONTROL_SHOOTING_ACTIVITY_ID);
    if ($controlShootingActivity && is_numeric($controlShootingActivity)) {
      $controlShootingActivity = $activityRepository->find($controlShootingActivity);
      if ($controlShootingActivity) {
        foreach ($todayPresentMembers as $memberPresence) {
          $presence = $memberPresenceRepository->findLastOneByActivity($memberPresence->getMember(), $controlShootingActivity);
          if ($presence) {
            $memberPresence->getMember()->setLastControlShooting($presence->getDate());
          }
        }
      }
    }

    return $todayPresentMembers;
  }

}
