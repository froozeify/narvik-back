<?php

namespace App\Tests\Story;

use App\Factory\SeasonFactory;
use Zenstruck\Foundry\Story;

final class SeasonStory extends Story {
  public const SEASONS = [
    "2019/2020",
    "2020/2021",
    "2021/2022",
    "2022/2023",
    "2023/2024",
    "2024/2025",
  ];

  public function build(): void {
    foreach (self::SEASONS as $season) {
      $this->addToPool('default_seasons', SeasonFactory::createOne([
        'name' => $season
      ]));
    }
  }
}
