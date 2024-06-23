<?php

namespace App\Story;

use App\Enum\GlobalSetting;
use App\Factory\GlobalSettingFactory;
use Zenstruck\Foundry\Story;
use function Zenstruck\Foundry\faker;

final class GlobalSettingStory extends Story {

  public function build(): void {
    $this->addToPool('required_settings', GlobalSettingFactory::createOne([
      'name'  => GlobalSetting::BADGER_TOKEN->name,
      'value' => faker()->regexify('[A-Za-z0-9-]{250}'),
    ]));

    $this->addToPool('required_settings', GlobalSettingFactory::createOne([
      'name'  => GlobalSetting::CONTROL_SHOOTING_ACTIVITY_ID->name,
      'value' => null,
    ]));
  }
}
