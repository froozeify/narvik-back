<?php

namespace App\DataFixtures;

use App\Factory\ExternalPresenceFactory;
use App\Factory\MemberFactory;
use App\Factory\MemberPresenceFactory;
use App\Factory\MemberSeasonFactory;
use App\Factory\SeasonFactory;
use App\Story\ActivityStory;
use App\Story\GlobalSettingStory;
use App\Story\SeasonStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Zenstruck\Foundry\faker;

class AppFixtures extends Fixture {
  public function load(ObjectManager $manager): void {
    // We generate the activities
    ActivityStory::load();

    // We create the default season
    SeasonStory::load();

    // We create the default global settings
    GlobalSettingStory::load();

    // We create the users
    MemberFactory::new()->admin("admin@admin.com")->create();
    MemberFactory::new()->badger()->create();
    MemberFactory::createMany(faker()->numberBetween(120, 420), [
      'memberPresences' => MemberPresenceFactory::new()->many(1, 4),
      'memberSeasons' => MemberSeasonFactory::new()->many(0, 4),
    ]);

    // We record some external presence
    ExternalPresenceFactory::new()->many(40, 80)->create();
  }
}
