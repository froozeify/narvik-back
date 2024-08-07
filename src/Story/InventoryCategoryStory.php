<?php

namespace App\Story;

use App\Factory\InventoryCategoryFactory;
use Zenstruck\Foundry\Story;

final class InventoryCategoryStory extends Story {
  public const CATEGORIES = [
    "Cibles",
    "Munitions",
    "Administratif",
    "Droit de tir",
    "Concours",
    "Autres",
  ];

  public function build(): void {
    foreach (self::CATEGORIES as $category) {
      $this->addToPool('default', InventoryCategoryFactory::createOne([
        'name' => $category
      ]));
    }
  }
}
