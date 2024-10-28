<?php

namespace App\DataFixtures;

use App\Factory\ClubFactory;
use App\Factory\ExternalPresenceFactory;
use App\Factory\InventoryCategoryFactory;
use App\Factory\InventoryItemFactory;
use App\Factory\InventoryItemHistoryFactory;
use App\Factory\MemberFactory;
use App\Factory\MemberPresenceFactory;
use App\Factory\MemberSeasonFactory;
use App\Factory\SaleFactory;
use App\Factory\SeasonFactory;
use App\Factory\UserFactory;
use App\Factory\UserMemberFactory;
use App\Story\ActivityStory;
use App\Story\GlobalSettingStory;
use App\Story\InventoryCategoryStory;
use App\Story\SalePaymentModeStory;
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
    $adminMember = UserFactory::new()->superAdmin("admin@admin.com")->create();
//    UserFactory::new()->badger()->create();

    // TODO: Create some clubs and links user to them
    ClubFactory::createMany(faker()->numberBetween(2, 5));
    $users = UserFactory::createMany(faker()->numberBetween(5, 10));

    // We create some member
    $members = MemberFactory::createMany(faker()->numberBetween(30, 40), [
      'memberPresences' => MemberPresenceFactory::new()->many(1, 4),
      'memberSeasons'   => MemberSeasonFactory::new()->many(0, 4),
    ]);

    foreach ($members as $member) {
      UserMemberFactory::createOne([
        'member' => $member,
      ]);
    }

    // We record some external presence
    ExternalPresenceFactory::new()->many(20, 40)->create();

    /*******************************************************
     *                    INVENTORY                        *
     ******************************************************/

    // We create the default season
    $defaultCategoriesPool = InventoryCategoryStory::load();

    $itemsMapping = [
      "Cibles" => ['Cible C50', 'Visuel C50', 'Pistolet 10M', 'Carabine 10M'],
      "Munitions" => ['semi-auto 22lr', '9mm - Sellier & Bellot', '9mm - Geco', 'Plombs'],
      "Administratif" => ['licence', 'droit d\'entrée', 'second club'],
      "Droit de tir" => ['10M', '25/50M'],
    ];
    $categories = $defaultCategoriesPool->getPool('default');
    foreach ($categories as $category) {
      $catName = $category->_real()->getName();
      if (array_key_exists($catName, $itemsMapping)) {
        foreach ($itemsMapping[$catName] as $name) {
          $item = InventoryItemFactory::createOne(['name' => $name, 'category' => $category]);
          InventoryItemHistoryFactory::new()->many(2, 6)->create(['item' => $item]);
        }
      } else {
        $items = InventoryItemFactory::new()->many(1, 5)->create(['category' => $category]);
        foreach ($items as $item) {
          InventoryItemHistoryFactory::new()->many(2, 6)->create(['item' => $item]);
        }
      }
    }

    SalePaymentModeStory::load();
//    SaleFactory::createMany(faker()->numberBetween(10, 30), [
//      'seller' => $clubSupervisor,
//    ]);
  }
}
