<?php

namespace App\Service;

class UtilsService {
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
