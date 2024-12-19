<?php

namespace App\Controller\Abstract;

use App\Entity\Club;
use App\Enum\ClubRole;
use App\Enum\UserRole;
use App\Service\RequestService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractClubDependentController extends AbstractController {
  private readonly Club $club;

  public function __construct(
    RequestStack $requestStack,
    private readonly RequestService $requestService,
  ) {
    $this->club = $this->getClub($requestStack->getCurrentRequest());
  }

  private function getClub(Request $request): Club {
    $club = $this->requestService->getClubFromRequest($request);
    if (!$club instanceof Club) {
      throw $this->createNotFoundException();
    }
    return $club;
  }

  public function getQueryClub(): Club {
    return $this->club;
  }
}
