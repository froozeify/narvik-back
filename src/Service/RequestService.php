<?php

namespace App\Service;

use App\Entity\Club;
use App\Repository\ClubRepository;
use Symfony\Component\HttpFoundation\Request;

final readonly class RequestService {
  public function __construct(
    private ClubRepository $clubRepository,
  ) {
  }

  public function getClubFromRequest(Request $request): ?Club {
    $uuid = $request->attributes->get("clubUuid");
    if ($uuid) {
      return $this->clubRepository->findOneByUuidRestrained($uuid);
    }
    return null;
  }
}
