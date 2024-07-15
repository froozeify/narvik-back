<?php

namespace App\Factory;

use App\Entity\InventoryItem;
use App\Repository\InventoryItemRepository;
use App\Story\InventoryCategoryStory;
use Zenstruck\Foundry\FactoryCollection;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 *
 * @method InventoryItem|Proxy create(array|callable $attributes = [])
 * @method static InventoryItem|Proxy createOne(array $attributes = [])
 * @method static InventoryItem|Proxy find(object|array|mixed $criteria)
 * @method static InventoryItem|Proxy findOrCreate(array $attributes)
 * @method static InventoryItem|Proxy first(string $sortedField = 'id')
 * @method static InventoryItem|Proxy last(string $sortedField = 'id')
 * @method static InventoryItem|Proxy random(array $attributes = [])
 * @method static InventoryItem|Proxy randomOrCreate(array $attributes = [])
 * @method static InventoryItem[]|Proxy[] all()
 * @method static InventoryItem[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static InventoryItem[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static InventoryItem[]|Proxy[] findBy(array $attributes)
 * @method static InventoryItem[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static InventoryItem[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method FactoryCollection<InventoryItem|Proxy> many(int $min, int|null $max = null)
 * @method FactoryCollection<InventoryItem|Proxy> sequence(iterable|callable $sequence)
 * @method static ProxyRepositoryDecorator<InventoryItem, InventoryItemRepository> repository()
 *
 * @phpstan-method \App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem> create(array|callable $attributes = [])
 * @phpstan-method static \App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem> createOne(array $attributes = [])
 * @phpstan-method static \App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem> find(object|array|mixed $criteria)
 * @phpstan-method static \App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem> findOrCreate(array $attributes)
 * @phpstan-method static \App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem> first(string $sortedField = 'id')
 * @phpstan-method static \App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem> last(string $sortedField = 'id')
 * @phpstan-method static \App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem> random(array $attributes = [])
 * @phpstan-method static \App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<\App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem>> all()
 * @phpstan-method static list<\App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<\App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<\App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem>> findBy(array $attributes)
 * @phpstan-method static list<\App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<\App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem>> randomSet(int $number, array $attributes = [])
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem>> many(int $min, int|null $max = null)
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Entity\InventoryItem&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryItem>> sequence(iterable|callable $sequence)
 * @extends PersistentProxyObjectFactory<InventoryItem>
 */
final class InventoryItemFactory extends PersistentProxyObjectFactory {
  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-service
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
   */
  protected function defaults(): array {
    return [
      'name'            => self::faker()->text(10),
      'description'     => self::faker()->boolean(40) ? self::faker()->words(self::faker()->numberBetween(4, 8), true) : null,
      'canBeSold'       => self::faker()->boolean(75),
      'category'        => InventoryCategoryStory::getRandom('default'),
      'purchasePrice'   => self::faker()->randomFloat(2, 1, 20),
      'sellingPrice'    => self::faker()->randomFloat(2, 20, 80),
      'sellingQuantity' => self::faker()->boolean(75) ? self::faker()->numberBetween(1, 5) : 1,
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static {
    return $this// ->afterInstantiate(function(InventoryItem $inventoryItem): void {})
      ;
  }

  public static function class(): string {
    return InventoryItem::class;
  }
}
