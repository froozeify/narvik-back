<?php

namespace App\Factory;

use App\Entity\SalePurchasedItem;
use App\Repository\SalePurchasedItemRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<SalePurchasedItem>
 *
 * @method        SalePurchasedItem|Proxy                              create(array|callable $attributes = [])
 * @method static SalePurchasedItem|Proxy                              createOne(array $attributes = [])
 * @method static SalePurchasedItem|Proxy                              find(object|array|mixed $criteria)
 * @method static SalePurchasedItem|Proxy                              findOrCreate(array $attributes)
 * @method static SalePurchasedItem|Proxy                              first(string $sortedField = 'id')
 * @method static SalePurchasedItem|Proxy                              last(string $sortedField = 'id')
 * @method static SalePurchasedItem|Proxy                              random(array $attributes = [])
 * @method static SalePurchasedItem|Proxy                              randomOrCreate(array $attributes = [])
 * @method static SalePurchasedItemRepository|ProxyRepositoryDecorator repository()
 * @method static SalePurchasedItem[]|Proxy[]                          all()
 * @method static SalePurchasedItem[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static SalePurchasedItem[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static SalePurchasedItem[]|Proxy[]                          findBy(array $attributes)
 * @method static SalePurchasedItem[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static SalePurchasedItem[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        SalePurchasedItem&Proxy<SalePurchasedItem> create(array|callable $attributes = [])
 * @phpstan-method static SalePurchasedItem&Proxy<SalePurchasedItem> createOne(array $attributes = [])
 * @phpstan-method static SalePurchasedItem&Proxy<SalePurchasedItem> find(object|array|mixed $criteria)
 * @phpstan-method static SalePurchasedItem&Proxy<SalePurchasedItem> findOrCreate(array $attributes)
 * @phpstan-method static SalePurchasedItem&Proxy<SalePurchasedItem> first(string $sortedField = 'id')
 * @phpstan-method static SalePurchasedItem&Proxy<SalePurchasedItem> last(string $sortedField = 'id')
 * @phpstan-method static SalePurchasedItem&Proxy<SalePurchasedItem> random(array $attributes = [])
 * @phpstan-method static SalePurchasedItem&Proxy<SalePurchasedItem> randomOrCreate(array $attributes = [])
 * @phpstan-method static ProxyRepositoryDecorator<SalePurchasedItem, EntityRepository> repository()
 * @phpstan-method static list<SalePurchasedItem&Proxy<SalePurchasedItem>> all()
 * @phpstan-method static list<SalePurchasedItem&Proxy<SalePurchasedItem>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<SalePurchasedItem&Proxy<SalePurchasedItem>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<SalePurchasedItem&Proxy<SalePurchasedItem>> findBy(array $attributes)
 * @phpstan-method static list<SalePurchasedItem&Proxy<SalePurchasedItem>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<SalePurchasedItem&Proxy<SalePurchasedItem>> randomSet(int $number, array $attributes = [])
 */
final class SalePurchasedItemFactory extends PersistentProxyObjectFactory {
  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
   */
  public function __construct() {
  }

  public static function class(): string {
    return SalePurchasedItem::class;
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
   */
  protected function defaults(): array|callable {
    return [
      'item' => InventoryItemFactory::random(),
      'quantity' => self::faker()->numberBetween(1, 10),
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static {
    return $this// ->afterInstantiate(function(SalePurchasedItem $salePurchasedItem): void {})
      ;
  }
}
