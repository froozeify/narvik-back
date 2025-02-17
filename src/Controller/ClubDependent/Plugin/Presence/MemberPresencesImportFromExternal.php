<?php

namespace App\Controller\ClubDependent\Plugin\Presence;

use App\Controller\Abstract\AbstractClubDependentController;
use App\Service\MemberPresenceService;
use Symfony\Component\HttpFoundation\JsonResponse;

class MemberPresencesImportFromExternal extends AbstractClubDependentController {

  public function __invoke(MemberPresenceService $memberPresenceService): JsonResponse {
    $totalImported = $memberPresenceService->importFromExternalPresence($this->getQueryClub());
    return new JsonResponse(["imported" => $totalImported]);
  }

}
