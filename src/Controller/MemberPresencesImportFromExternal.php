<?php

namespace App\Controller;

use App\Service\ImportCerbereService;
use App\Service\ImageService;
use App\Service\MemberPresenceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MemberPresencesImportFromExternal extends AbstractController {

  public function __invoke(MemberPresenceService $memberPresenceService): JsonResponse {
    $totalImported = $memberPresenceService->importFromExternalPresence();
    return new JsonResponse(["imported" => $totalImported]);
  }

}
