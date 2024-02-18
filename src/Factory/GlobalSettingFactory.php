<?php

namespace App\Factory;

use App\Entity\GlobalSetting;
use App\Repository\GlobalSettingRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<GlobalSetting>
 *
 * @method        GlobalSetting|Proxy                     create(array|callable $attributes = [])
 * @method static GlobalSetting|Proxy                     createOne(array $attributes = [])
 * @method static GlobalSetting|Proxy                     find(object|array|mixed $criteria)
 * @method static GlobalSetting|Proxy                     findOrCreate(array $attributes)
 * @method static GlobalSetting|Proxy                     first(string $sortedField = 'id')
 * @method static GlobalSetting|Proxy                     last(string $sortedField = 'id')
 * @method static GlobalSetting|Proxy                     random(array $attributes = [])
 * @method static GlobalSetting|Proxy                     randomOrCreate(array $attributes = [])
 * @method static GlobalSettingRepository|RepositoryProxy repository()
 * @method static GlobalSetting[]|Proxy[]                 all()
 * @method static GlobalSetting[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static GlobalSetting[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static GlobalSetting[]|Proxy[]                 findBy(array $attributes)
 * @method static GlobalSetting[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static GlobalSetting[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class GlobalSettingFactory extends ModelFactory {
  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
   */
  protected function getDefaults(): array {
    return [
      'name'  => self::faker()->text(255),
      'value' => self::faker()->text(255),
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): self {
    return $this// ->afterInstantiate(function(GlobalSetting $globalSetting): void {})
      ;
  }

  protected static function getClass(): string {
    return GlobalSetting::class;
  }
}
