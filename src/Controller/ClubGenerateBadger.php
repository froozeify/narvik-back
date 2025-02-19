<?php

namespace App\Controller;

use App\Controller\Abstract\AbstractController;
use App\Entity\Club;
use App\Service\UtilsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

class ClubGenerateBadger extends AbstractController {

  public function __invoke(#[MapEntity(mapping: ['uuid' => 'uuid'])] Club $club, EntityManagerInterface $entityManager, UtilsService $utilsService): Club {
    $club->setBadgerToken($utilsService->generateRandomToken(mt_rand(180, 200)));
    $entityManager->persist($club);
    $entityManager->flush();
    return $club;
  }

}
