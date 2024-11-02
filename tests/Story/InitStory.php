<?php

namespace App\Tests\Story;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UserStory extends Story {
  public function build(): void {
    $this->addToPool('super_admin', UserFactory::new()->superAdmin("admin@admin.com")->create());
    // UserFactory::new()->badger()->create();

    //TODO: Maybe rename it to InitStory with
    // Some clubs and user linked to them
//    ClubFactory::createMany(faker()->numberBetween(2, 5));
//    $users = UserFactory::createMany(faker()->numberBetween(5, 10));
  }
}
