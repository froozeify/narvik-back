<?php

namespace App\Controller;

use App\Enum\GlobalSetting;
use App\Service\GlobalSettingService;
use App\Service\ImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GlobalSettingImportLogo extends AbstractController {

  public function __invoke(Request $request, ImageService $imageService, GlobalSettingService $globalSettingService): JsonResponse {
    /** @var UploadedFile|null $uploadedFile */
    $uploadedFile = $request->files->get('file');

    // Deleting current logo
    if (!$uploadedFile) {
      $globalSettingService->updateSettingValue(GlobalSetting::LOGO, '');
      return new JsonResponse();
    }

    if (strtolower($uploadedFile->getClientOriginalExtension()) !== "png") {
      throw new BadRequestHttpException('The "file" must be a PNG');
    }

    $imageService->importLogo($uploadedFile);

    // For the moment the path is hardcoded
    $globalSettingService->updateSettingValue(GlobalSetting::LOGO, base64_encode("logo.png"));

    return new JsonResponse();
  }

}
