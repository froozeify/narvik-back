<?php

namespace App\Controller;

use App\Service\ImportItacCsvService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MemberImportFromItac extends AbstractController {

  public function __invoke(Request $request, ImportItacCsvService $itacCsvService): JsonResponse {
    /** @var UploadedFile|null $uploadedFile */
    $uploadedFile = $request->files->get('file');
    if (!$uploadedFile) {
      throw new BadRequestHttpException('"file" is required');
    }

    if (strtolower($uploadedFile->getClientOriginalExtension()) !== "csv") {
      throw new BadRequestHttpException('The "file" must be a CSV');
    }

    $response = $itacCsvService->importFromFile($uploadedFile->getPathname());

    return new JsonResponse(["lines" => $response]);
  }

}
