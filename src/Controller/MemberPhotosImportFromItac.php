<?php

namespace App\Controller;

use App\Service\MemberPhotoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MemberPhotosImportFromItac extends AbstractController {

  public function __invoke(Request $request, MemberPhotoService $importPhotosService): JsonResponse {
    /** @var UploadedFile|null $uploadedFile */
    $uploadedFile = $request->files->get('file');
    if (!$uploadedFile) {
      throw new BadRequestHttpException('"file" is required');
    }

    if (strtolower($uploadedFile->getClientOriginalExtension()) !== "zip") {
      throw new BadRequestHttpException('The "file" must be a ZIP');
    }

    $importPhotosService->importFromFile($uploadedFile);

    return new JsonResponse();
  }

}
