<?php

namespace App\Controller\ClubDependent\Plugin\Presence;

use App\Controller\Abstract\AbstractClubDependentController;
use App\Repository\ExternalPresenceRepository;

class ExternalPresenceToday extends AbstractClubDependentController {

  public function __invoke(ExternalPresenceRepository $externalPresenceRepository): ?array {
    return $externalPresenceRepository->findAllPresentToday($this->getQueryClub());
  }

}
