<?php

namespace App\Controller\ClubDependent;

use App\Controller\Abstract\AbstractClubDependentController;
use App\Service\ImageService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ClubSettingImportLogo extends AbstractClubDependentController {

  public function __invoke(Request $request, ImageService $importPhotosService): JsonResponse {
    /** @var UploadedFile|null $uploadedFile */
    $uploadedFile = $request->files->get('file');
    if (!$uploadedFile) {
      throw new BadRequestHttpException('"file" is required');
    }

    $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp'];

    if (!in_array($uploadedFile->getClientOriginalExtension(), $allowedExtensions)) {
      throw new BadRequestHttpException('The "file" must be an image (png, jpg, webp).');
    }

    $importPhotosService->importClubLogo($this->getQueryClub(), $uploadedFile);

    return new JsonResponse();
  }

}
