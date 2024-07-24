<?php

namespace App\Factory;

use App\Entity\SalePaymentMode;
use App\Repository\SalePaymentModeRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<SalePaymentMode>
 *
 * @method        SalePaymentMode|Proxy                              create(array|callable $attributes = [])
 * @method static SalePaymentMode|Proxy                              createOne(array $attributes = [])
 * @method static SalePaymentMode|Proxy                              find(object|array|mixed $criteria)
 * @method static SalePaymentMode|Proxy                              findOrCreate(array $attributes)
 * @method static SalePaymentMode|Proxy                              first(string $sortedField = 'id')
 * @method static SalePaymentMode|Proxy                              last(string $sortedField = 'id')
 * @method static SalePaymentMode|Proxy                              random(array $attributes = [])
 * @method static SalePaymentMode|Proxy                              randomOrCreate(array $attributes = [])
 * @method static SalePaymentModeRepository|ProxyRepositoryDecorator repository()
 * @method static SalePaymentMode[]|Proxy[]                          all()
 * @method static SalePaymentMode[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static SalePaymentMode[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static SalePaymentMode[]|Proxy[]                          findBy(array $attributes)
 * @method static SalePaymentMode[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static SalePaymentMode[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        SalePaymentMode&Proxy<SalePaymentMode> create(array|callable $attributes = [])
 * @phpstan-method static SalePaymentMode&Proxy<SalePaymentMode> createOne(array $attributes = [])
 * @phpstan-method static SalePaymentMode&Proxy<SalePaymentMode> find(object|array|mixed $criteria)
 * @phpstan-method static SalePaymentMode&Proxy<SalePaymentMode> findOrCreate(array $attributes)
 * @phpstan-method static SalePaymentMode&Proxy<SalePaymentMode> first(string $sortedField = 'id')
 * @phpstan-method static SalePaymentMode&Proxy<SalePaymentMode> last(string $sortedField = 'id')
 * @phpstan-method static SalePaymentMode&Proxy<SalePaymentMode> random(array $attributes = [])
 * @phpstan-method static SalePaymentMode&Proxy<SalePaymentMode> randomOrCreate(array $attributes = [])
 * @phpstan-method static ProxyRepositoryDecorator<SalePaymentMode, EntityRepository> repository()
 * @phpstan-method static list<SalePaymentMode&Proxy<SalePaymentMode>> all()
 * @phpstan-method static list<SalePaymentMode&Proxy<SalePaymentMode>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<SalePaymentMode&Proxy<SalePaymentMode>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<SalePaymentMode&Proxy<SalePaymentMode>> findBy(array $attributes)
 * @phpstan-method static list<SalePaymentMode&Proxy<SalePaymentMode>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<SalePaymentMode&Proxy<SalePaymentMode>> randomSet(int $number, array $attributes = [])
 */
final class SalePaymentModeFactory extends PersistentProxyObjectFactory {
  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
   *
   */
  public function __construct() {
  }

  public static function class(): string {
    return SalePaymentMode::class;
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
   *
   */
  protected function defaults(): array|callable {
    return [
      'available' => self::faker()->boolean(75),
      'name'      => self::faker()->text(12),
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static {
    return $this// ->afterInstantiate(function(SalePaymentMode $salePaymentMode): void {})
      ;
  }
}
