<?php

namespace App\Controller\Abstract;

use App\Entity\Club;
use App\Service\RequestService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractClubDependentController extends AbstractController {
  private Club $club;

  public function __construct(
    RequestStack $requestStack,
    private readonly RequestService $requestService,
  ) {
    $this->club = $this->getClub($requestStack->getCurrentRequest());
  }

  private function getClub(Request $request): Club {
    $club = $this->requestService->getClubFromRequest($request);
    if (!$club instanceof Club) {
      dump('dsds');
      throw $this->createAccessDeniedException();
    }
    return $club;
  }

  public function getQueryClub(): Club {
    return $this->club;
  }
}
