<?php

namespace App\Tests\Story;

use App\Factory\ActivityFactory;
use Zenstruck\Foundry\Story;

final class ActivityStory extends Story {

  public function build(): void {
    foreach (ActivityFactory::ACTIVITIES as $activity) {
      $this->addToPool('default_activities', ActivityFactory::createOne([
        'name' => $activity
      ]));
    }
  }
}
