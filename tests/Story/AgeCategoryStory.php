<?php

namespace App\Tests\Story;

use App\Tests\Factory\AgeCategoryFactory;
use Zenstruck\Foundry\Story;

final class AgeCategoryStory extends Story {
  private const array DEFAULT_AGE_CATEGORIES = [
    "S1" => "Senior 1",
    "S2" => "Senior 2",
    "S3" => "Senior 3",

    "D1" => "Dame 1",
    "D2" => "Dame 2",
    "D3" => "Dame 3",

    "JG" => "Junior Garçon",
    "JF" => "Junior Fille",

    "CG" => "Cadet Garçon",
    "CF" => "Cadet Fille",

    "MG" => "Minime Garçon",
    "MF" => "Minime Fille",

    "BG" => "Benjamin Garçon",
    "BF" => "Benjamin Fille",

    "PG" => "Poussin Garçon",
    "PF" => "Poussin Fille",
  ];

  public function build(): void {
    foreach (self::DEFAULT_AGE_CATEGORIES as $code => $name) {
      $this->addToPool('age_categories', AgeCategoryFactory::createOne([
        'code' => $code,
        'name' => $name,
      ]));
    }
  }
}
