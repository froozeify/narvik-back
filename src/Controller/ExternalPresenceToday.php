<?php

namespace App\Controller;

use App\Repository\ExternalPresenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ExternalPresenceToday extends AbstractController {

  public function __invoke(Request $request, ExternalPresenceRepository $externalPresenceRepository): ?array {
    return $externalPresenceRepository->findAllPresentToday();
  }

}
