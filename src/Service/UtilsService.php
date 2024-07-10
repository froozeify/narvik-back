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

  public static function convertStringToDbDecimal(?string $string): ?string {
    if (empty($string)) {
      return null;
    }

    return filter_var(str_replace(',', '.', $string), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
  }
}
