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
  private Club $club;

  public function __construct(
    RequestStack $requestStack,
    private readonly Security $security,
    private readonly RequestService $requestService,
  ) {
    $this->club = $this->getClub($requestStack->getCurrentRequest());

    $granted = false;
    foreach (static::MINIMUM_ROLES() as $role) {
      if ($this->security->isGranted($role->value, $this->club)) {
        $granted = true;
        break;
      }
    }

    if (!$granted) {
      throw $this->createAccessDeniedException();
    }
  }

  /**
   * @return ClubRole[]|UserRole[]
   */
  abstract public static function MINIMUM_ROLES(): array;

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
