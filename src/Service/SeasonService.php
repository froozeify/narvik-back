<?php

namespace App\Service;

use App\Entity\Season;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;

class SeasonService {
  public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly SeasonRepository $seasonRepository,
  ) {
  }


  public function getOrCreateSeason(string $seasonName, bool $autoFlush = true): ?Season {
    $seasonName = trim(str_replace(" ", "", $seasonName));
    // Season name must be in format 20xx/20xx
    if (strlen($seasonName) !== 9) {
      return null;
    }

    $seasons = explode("/", $seasonName, 2);
    if (!is_numeric($seasons[0]) || !is_numeric($seasons[1])) {
      return null;
    }

    $seasonName = "$seasons[0]/$seasons[1]";
    $season = $this->seasonRepository->findOneByName($seasonName);
    if (!$season) {
      $season = new Season();
      $season->setName("$seasons[0]/$seasons[1]");
      $this->entityManager->persist($season);

      if ($autoFlush) {
        $this->entityManager->flush();
      }
    }

    return $season;
  }

  public static function getCurrentSeasonName(): string {
    $today = new \DateTimeImmutable();
    $endYearSeason = new \DateTimeImmutable("31 august");
    $seasonName = "";
    if ($today < $endYearSeason) {
      $seasonName = $today->modify("-1year")->format("Y") . "/" . $today->format("Y");
    } else {
      $seasonName = $today->format("Y") . "/" . $today->modify("+1year")->format("Y");
    }
    return $seasonName;
  }

  public static function getPreviousSeasonName(): string {
    $seasons = explode("/", self::getCurrentSeasonName());
    $seasons[0] = --$seasons[0];
    $seasons[1] = --$seasons[1];

    return implode("/", $seasons);
  }
}
