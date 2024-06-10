<?php

namespace App\Factory;

use App\Entity\InventoryCategory;
use App\Repository\InventoryCategoryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<InventoryCategory>
 *
 * @method        InventoryCategory|Proxy                     create(array|callable $attributes = [])
 * @method static InventoryCategory|Proxy                     createOne(array $attributes = [])
 * @method static InventoryCategory|Proxy                     find(object|array|mixed $criteria)
 * @method static InventoryCategory|Proxy                     findOrCreate(array $attributes)
 * @method static InventoryCategory|Proxy                     first(string $sortedField = 'id')
 * @method static InventoryCategory|Proxy                     last(string $sortedField = 'id')
 * @method static InventoryCategory|Proxy                     random(array $attributes = [])
 * @method static InventoryCategory|Proxy                     randomOrCreate(array $attributes = [])
 * @method static InventoryCategoryRepository|RepositoryProxy repository()
 * @method static InventoryCategory[]|Proxy[]                 all()
 * @method static InventoryCategory[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static InventoryCategory[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static InventoryCategory[]|Proxy[]                 findBy(array $attributes)
 * @method static InventoryCategory[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static InventoryCategory[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class InventoryCategoryFactory extends ModelFactory {
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
      'name'   => self::faker()->text(10),
      'weight' => mt_rand(1, 20),
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): self {
    return $this// ->afterInstantiate(function(InventoryCategory $inventoryCategory): void {})
      ;
  }

  protected static function getClass(): string {
    return InventoryCategory::class;
  }
}
