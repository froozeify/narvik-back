<?php

namespace App\DataFixtures;

use App\Tests\Factory\ExternalPresenceFactory;
use App\Tests\Factory\InventoryItemFactory;
use App\Tests\Factory\InventoryItemHistoryFactory;
use App\Tests\Story\_InitStory;
use App\Tests\Story\ActivityStory;
use App\Tests\Story\AgeCategoryStory;
use App\Tests\Story\GlobalSettingStory;
use App\Tests\Story\InventoryCategoryStory;
use App\Tests\Story\SalePaymentModeStory;
use App\Tests\Story\SeasonStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture {
  public function load(ObjectManager $manager): void {
    // We create the bare minium required (some users and clubs)
    _InitStory::load();

    // We create the default season
    SeasonStory::load();

    // We create the default global settings
    GlobalSettingStory::load();

    // We generate the activities
    ActivityStory::load();

    AgeCategoryStory::load();

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
