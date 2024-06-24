<?php

namespace App\Factory;

use App\Entity\InventoryCategory;
use App\Repository\InventoryCategoryRepository;
use Zenstruck\Foundry\FactoryCollection;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 *
 * @method InventoryCategory|Proxy create(array|callable $attributes = [])
 * @method static InventoryCategory|Proxy createOne(array $attributes = [])
 * @method static InventoryCategory|Proxy find(object|array|mixed $criteria)
 * @method static InventoryCategory|Proxy findOrCreate(array $attributes)
 * @method static InventoryCategory|Proxy first(string $sortedField = 'id')
 * @method static InventoryCategory|Proxy last(string $sortedField = 'id')
 * @method static InventoryCategory|Proxy random(array $attributes = [])
 * @method static InventoryCategory|Proxy randomOrCreate(array $attributes = [])
 * @method static InventoryCategory[]|Proxy[] all()
 * @method static InventoryCategory[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static InventoryCategory[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static InventoryCategory[]|Proxy[] findBy(array $attributes)
 * @method static InventoryCategory[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static InventoryCategory[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method FactoryCollection<InventoryCategory|Proxy> many(int $min, int|null $max = null)
 * @method FactoryCollection<InventoryCategory|Proxy> sequence(iterable|callable $sequence)
 * @method static ProxyRepositoryDecorator<InventoryCategory, InventoryCategoryRepository> repository()
 *
 * @phpstan-method \App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory> create(array|callable $attributes = [])
 * @phpstan-method static \App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory> createOne(array $attributes = [])
 * @phpstan-method static \App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory> find(object|array|mixed $criteria)
 * @phpstan-method static \App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory> findOrCreate(array $attributes)
 * @phpstan-method static \App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory> first(string $sortedField = 'id')
 * @phpstan-method static \App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory> last(string $sortedField = 'id')
 * @phpstan-method static \App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory> random(array $attributes = [])
 * @phpstan-method static \App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<\App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory>> all()
 * @phpstan-method static list<\App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<\App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<\App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory>> findBy(array $attributes)
 * @phpstan-method static list<\App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<\App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory>> randomSet(int $number, array $attributes = [])
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory>> many(int $min, int|null $max = null)
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Entity\InventoryCategory&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\InventoryCategory>> sequence(iterable|callable $sequence)
 * @extends PersistentProxyObjectFactory<InventoryCategory>
 */
final class InventoryCategoryFactory extends PersistentProxyObjectFactory {
  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
   */
  protected function defaults(): array {
    return [
      'name'   => self::faker()->text(10),
      'weight' => mt_rand(1, 20),
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static {
    return $this// ->afterInstantiate(function(InventoryCategory $inventoryCategory): void {})
      ;
  }

  public static function class(): string {
    return InventoryCategory::class;
  }
}
