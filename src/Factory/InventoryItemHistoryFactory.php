<?php

namespace App\Factory;

use App\Entity\InventoryItemHistory;
use App\Repository\InventoryItemHistoryRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<InventoryItemHistory>
 *
 * @method        InventoryItemHistory|Proxy                              create(array|callable $attributes = [])
 * @method static InventoryItemHistory|Proxy                              createOne(array $attributes = [])
 * @method static InventoryItemHistory|Proxy                              find(object|array|mixed $criteria)
 * @method static InventoryItemHistory|Proxy                              findOrCreate(array $attributes)
 * @method static InventoryItemHistory|Proxy                              first(string $sortedField = 'id')
 * @method static InventoryItemHistory|Proxy                              last(string $sortedField = 'id')
 * @method static InventoryItemHistory|Proxy                              random(array $attributes = [])
 * @method static InventoryItemHistory|Proxy                              randomOrCreate(array $attributes = [])
 * @method static InventoryItemHistoryRepository|ProxyRepositoryDecorator repository()
 * @method static InventoryItemHistory[]|Proxy[]                          all()
 * @method static InventoryItemHistory[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static InventoryItemHistory[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static InventoryItemHistory[]|Proxy[]                          findBy(array $attributes)
 * @method static InventoryItemHistory[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static InventoryItemHistory[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        InventoryItemHistory&Proxy<InventoryItemHistory> create(array|callable $attributes = [])
 * @phpstan-method static InventoryItemHistory&Proxy<InventoryItemHistory> createOne(array $attributes = [])
 * @phpstan-method static InventoryItemHistory&Proxy<InventoryItemHistory> find(object|array|mixed $criteria)
 * @phpstan-method static InventoryItemHistory&Proxy<InventoryItemHistory> findOrCreate(array $attributes)
 * @phpstan-method static InventoryItemHistory&Proxy<InventoryItemHistory> first(string $sortedField = 'id')
 * @phpstan-method static InventoryItemHistory&Proxy<InventoryItemHistory> last(string $sortedField = 'id')
 * @phpstan-method static InventoryItemHistory&Proxy<InventoryItemHistory> random(array $attributes = [])
 * @phpstan-method static InventoryItemHistory&Proxy<InventoryItemHistory> randomOrCreate(array $attributes = [])
 * @phpstan-method static ProxyRepositoryDecorator<InventoryItemHistory, EntityRepository> repository()
 * @phpstan-method static list<InventoryItemHistory&Proxy<InventoryItemHistory>> all()
 * @phpstan-method static list<InventoryItemHistory&Proxy<InventoryItemHistory>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<InventoryItemHistory&Proxy<InventoryItemHistory>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<InventoryItemHistory&Proxy<InventoryItemHistory>> findBy(array $attributes)
 * @phpstan-method static list<InventoryItemHistory&Proxy<InventoryItemHistory>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<InventoryItemHistory&Proxy<InventoryItemHistory>> randomSet(int $number, array $attributes = [])
 */
final class InventoryItemHistoryFactory extends PersistentProxyObjectFactory {
  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
   */
  public function __construct() {
    parent::__construct();
  }

  public static function class(): string {
    return InventoryItemHistory::class;
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
   */
  protected function defaults(): array|callable {
    return [
      'date' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-2 years')),
      'item' => InventoryItemFactory::random(),
      'purchasePrice' => self::faker()->randomFloat(2, 1, 20),
      'sellingPrice' => self::faker()->randomFloat(2, 20, 80),
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static {
    return $this// ->afterInstantiate(function(InventoryItemHistory $inventoryItemHistory): void {})
      ;
  }
}
