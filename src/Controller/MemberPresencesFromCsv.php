<?php

namespace App\Controller;

use App\Importer\ImportMemberPresence;
use App\Service\ImportCerbereService;
use App\Service\ImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MemberPresencesFromCsv extends AbstractController {

  public function __invoke(Request $request, ImportMemberPresence $importMemberPresence): JsonResponse {
    /** @var UploadedFile|null $uploadedFile */
    $uploadedFile = $request->files->get('file');
    if (!$uploadedFile) {
      throw new BadRequestHttpException('"file" is required');
    }

    if (strtolower($uploadedFile->getClientOriginalExtension()) !== "csv") {
      throw new BadRequestHttpException('The "file" must be a csv');
    }

    $response = $importMemberPresence->fromFile($uploadedFile);

    return new JsonResponse($response);
  }

}
