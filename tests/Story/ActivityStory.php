<?php

namespace App\Tests\Story;

use App\Tests\Factory\ActivityFactory;
use Zenstruck\Foundry\Story;

final class ActivityStory extends Story {

  public function build(): void {
    foreach (ActivityFactory::ACTIVITIES as $activity) {
      $this->addToPool('activities_club1', ActivityFactory::createOne([
        'name' => $activity,
        'club' => _InitStory::club_1()
      ]));
    }

    $this->addToPool('activities_club2', ActivityFactory::createOne([
      'name' => ActivityFactory::ACTIVITIES[0],
      'club' => _InitStory::club_2()
    ]));
  }
}
