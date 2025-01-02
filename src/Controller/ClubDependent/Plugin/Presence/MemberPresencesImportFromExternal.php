<?php

namespace App\Controller\ClubDependent\Plugin\Presence;

use App\Service\MemberPresenceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MemberPresencesImportFromExternal extends AbstractController {

  public function __invoke(MemberPresenceService $memberPresenceService): JsonResponse {
    $totalImported = $memberPresenceService->importFromExternalPresence();
    return new JsonResponse(["imported" => $totalImported]);
  }

}
