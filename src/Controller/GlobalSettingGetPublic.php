<?php

namespace App\Controller;

use App\Entity\GlobalSetting;
use App\Enum\GlobalSetting as GlobalSettingEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GlobalSettingGetPublic extends AbstractController {
  public const AVAILABLE_PUBLICLY = [
    GlobalSettingEnum::LOGO->name
  ];

  public function __invoke(GlobalSetting $globalSetting): ?GlobalSetting {
    if (!in_array($globalSetting->getName(), self::AVAILABLE_PUBLICLY)) {
      throw new NotFoundHttpException();
    }

    return $globalSetting;
  }

}
