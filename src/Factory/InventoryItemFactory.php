<?php

namespace App\Factory;

use App\Entity\InventoryItem;
use App\Repository\InventoryItemRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<InventoryItem>
 *
 * @method        InventoryItem|Proxy                     create(array|callable $attributes = [])
 * @method static InventoryItem|Proxy                     createOne(array $attributes = [])
 * @method static InventoryItem|Proxy                     find(object|array|mixed $criteria)
 * @method static InventoryItem|Proxy                     findOrCreate(array $attributes)
 * @method static InventoryItem|Proxy                     first(string $sortedField = 'id')
 * @method static InventoryItem|Proxy                     last(string $sortedField = 'id')
 * @method static InventoryItem|Proxy                     random(array $attributes = [])
 * @method static InventoryItem|Proxy                     randomOrCreate(array $attributes = [])
 * @method static InventoryItemRepository|RepositoryProxy repository()
 * @method static InventoryItem[]|Proxy[]                 all()
 * @method static InventoryItem[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static InventoryItem[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static InventoryItem[]|Proxy[]                 findBy(array $attributes)
 * @method static InventoryItem[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static InventoryItem[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class InventoryItemFactory extends ModelFactory {
  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-service
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
   */
  protected function getDefaults(): array {
    return [
      'name'          => self::faker()->text(10),
      'canBeSold'     => self::faker()->boolean(),
      'category'      => InventoryCategoryFactory::randomOrCreate(),
      'purchasePrice' => self::faker()->randomFloat(2, 1, 20),
      'sellingPrice'  => self::faker()->randomFloat(2, 20, 80),
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): self {
    return $this// ->afterInstantiate(function(InventoryItem $inventoryItem): void {})
      ;
  }

  protected static function getClass(): string {
    return InventoryItem::class;
  }
}
