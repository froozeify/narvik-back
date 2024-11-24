<?php

namespace App\Tests\Story;

use App\Tests\Factory\AgeCategoryFactory;
use Zenstruck\Foundry\Story;

final class AgeCategoryStory extends Story {
  public function build(): void {
    $this->addToPool('age_categories', AgeCategoryFactory::createOne([
      'code' => 'S1',
      'name' => 'Senior 1',
    ]));
    $this->addToPool('age_categories', AgeCategoryFactory::createOne([
      'code' => 'S2',
      'name' => 'Senior 2',
    ]));
    $this->addToPool('age_categories', AgeCategoryFactory::createOne([
      'code' => 'S3',
      'name' => 'Senior 3',
    ]));
  }
}
