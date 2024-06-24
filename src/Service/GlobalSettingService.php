<?php

namespace App\Service;

use App\Entity\GlobalSetting as GlobalSettingEntity;
use App\Enum\GlobalSetting;
use Doctrine\ORM\EntityManagerInterface;

class GlobalSettingService {
  public function __construct(
    private readonly EntityManagerInterface $em
  ) {

  }

  public function getSettingValue(GlobalSetting $setting): ?string {
    $dbSetting = $this->em->getRepository(GlobalSettingEntity::class)
      ->findOneBy([
        "name" => $setting->name
      ]);

    if (!$dbSetting) {
      return null;
    }

    return $dbSetting->getValue();
  }

  public function getRequiredSettingValue(GlobalSetting $setting): string {
    $dbSetting = $this->getSettingValue($setting);
    if (empty($dbSetting)) {
      throw new \Exception("Required GlobalSetting \"{$setting->name}\" not defined");
    }

    return $dbSetting;
  }

  public function updateSettingValue(GlobalSetting $setting, ?string $value): void {
    $dbSetting = $this->em->getRepository(GlobalSettingEntity::class)
      ->findOneBy([
        "name" => $setting->name
      ]);

    if (!$dbSetting) {
      $dbSetting = new GlobalSettingEntity();
      $dbSetting->setName($setting->name);
    }

    $dbSetting->setValue($value);
    $this->em->persist($dbSetting);
    $this->em->flush();
  }
}
