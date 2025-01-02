<?php

namespace App\Controller\ClubDependent\Plugin\Presence;

use App\Repository\ExternalPresenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExternalPresenceToday extends AbstractController {

  public function __invoke(ExternalPresenceRepository $externalPresenceRepository): ?array {
    return $externalPresenceRepository->findAllPresentToday();
  }

}
