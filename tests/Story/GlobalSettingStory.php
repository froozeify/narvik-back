<?php

namespace App\Tests\Story;

use App\Enum\GlobalSetting;
use App\Tests\Factory\GlobalSettingFactory;
use Zenstruck\Foundry\Story;
use function Zenstruck\Foundry\faker;

final class GlobalSettingStory extends Story {

  public function build(): void {
//    $this->addToPool('required_settings', GlobalSettingFactory::createOne([
//      'name'  => GlobalSetting::BADGER_TOKEN->name,
//      'value' => faker()->regexify('[A-Za-z0-9-]{250}'),
//    ]));

    $this->addToPool('required_settings', GlobalSettingFactory::createOne([
      'name'  => GlobalSetting::CONTROL_SHOOTING_ACTIVITY_ID->name,
      'value' => null,
    ]));

    $this->addToPool('required_settings', GlobalSettingFactory::createOne([
      'name'  => GlobalSetting::IGNORED_ACTIVITIES_OPENING_STATS->name,
      'value' => null,
    ]));

    $this->addToPool('required_settings', GlobalSettingFactory::createOne([
      'name'  => GlobalSetting::SMTP_ON->name,
      'value' => '1',
    ]));

    $this->addToPool('required_settings', GlobalSettingFactory::createOne([
      'name'  => GlobalSetting::SMTP_HOST->name,
      'value' => 'mail',
    ]));

    $this->addToPool('required_settings', GlobalSettingFactory::createOne([
      'name'  => GlobalSetting::SMTP_PORT->name,
      'value' => '1025',
    ]));

    $this->addToPool('required_settings', GlobalSettingFactory::createOne([
      'name'  => GlobalSetting::SMTP_SENDER->name,
      'value' => 'narvik@example.com',
    ]));
  }
}
