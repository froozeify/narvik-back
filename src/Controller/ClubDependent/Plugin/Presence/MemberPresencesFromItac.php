<?php

namespace App\Controller\ClubDependent\Plugin\Presence;

use App\Service\ImportCerbereService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MemberPresencesFromItac extends AbstractController {

  public function __invoke(Request $request, ImportCerbereService $importCerbereService): JsonResponse {
    /** @var UploadedFile|null $uploadedFile */
    $uploadedFile = $request->files->get('file');
    if (!$uploadedFile) {
      throw new BadRequestHttpException('"file" is required');
    }

    if (strtolower($uploadedFile->getClientOriginalExtension()) !== "xls") {
      throw new BadRequestHttpException('The "file" must be a xls');
    }

    $response = $importCerbereService->importFromFile($uploadedFile->getPathname());

    return new JsonResponse(["days" => $response]);
  }

}
