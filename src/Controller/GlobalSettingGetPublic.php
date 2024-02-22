<?php

namespace App\Controller;

use App\Entity\GlobalSetting;
use App\Enum\GlobalSetting as GlobalSettingEnum;
use App\Service\GlobalSettingService;
use App\Service\ImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GlobalSettingGetPublic extends AbstractController {
  const AVAILABLE_PUBLICLY = [
    GlobalSettingEnum::LOGO->name
  ];

  public function __invoke(GlobalSetting $globalSetting): GlobalSetting {
    if (!in_array($globalSetting->getName(), self::AVAILABLE_PUBLICLY)) {
      throw new NotFoundHttpException();
    }

    return $globalSetting;
  }

}
