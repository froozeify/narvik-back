<?php

namespace App\Factory;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use Zenstruck\Foundry\FactoryCollection;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 *
 * @method Activity|Proxy create(array|callable $attributes = [])
 * @method static Activity|Proxy createOne(array $attributes = [])
 * @method static Activity|Proxy find(object|array|mixed $criteria)
 * @method static Activity|Proxy findOrCreate(array $attributes)
 * @method static Activity|Proxy first(string $sortedField = 'id')
 * @method static Activity|Proxy last(string $sortedField = 'id')
 * @method static Activity|Proxy random(array $attributes = [])
 * @method static Activity|Proxy randomOrCreate(array $attributes = [])
 * @method static Activity[]|Proxy[] all()
 * @method static Activity[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Activity[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Activity[]|Proxy[] findBy(array $attributes)
 * @method static Activity[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Activity[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method FactoryCollection<Activity|Proxy> many(int $min, int|null $max = null)
 * @method FactoryCollection<Activity|Proxy> sequence(iterable|callable $sequence)
 * @method static ProxyRepositoryDecorator<Activity, ActivityRepository> repository()
 *
 * @phpstan-method \App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity> create(array|callable $attributes = [])
 * @phpstan-method static \App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity> createOne(array $attributes = [])
 * @phpstan-method static \App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity> find(object|array|mixed $criteria)
 * @phpstan-method static \App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity> findOrCreate(array $attributes)
 * @phpstan-method static \App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity> first(string $sortedField = 'id')
 * @phpstan-method static \App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity> last(string $sortedField = 'id')
 * @phpstan-method static \App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity> random(array $attributes = [])
 * @phpstan-method static \App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<\App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity>> all()
 * @phpstan-method static list<\App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<\App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<\App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity>> findBy(array $attributes)
 * @phpstan-method static list<\App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<\App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity>> randomSet(int $number, array $attributes = [])
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity>> many(int $min, int|null $max = null)
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Entity\Activity&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Activity>> sequence(iterable|callable $sequence)
 * @extends PersistentProxyObjectFactory<Activity>
 */
final class ActivityFactory extends PersistentProxyObjectFactory {
  public const ACTIVITIES = [
    '10M',
    '25M rameneurs',
    '25M toutes armes',
    '50M rameneurs',
    '50M 22Hunter',
    'Tir Contrôlé',
    'Permanence',
    'Travaux',
    'Bureau',
  ];

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
      'club' => ClubFactory::random(),
      'name' => self::faker()->randomElement(self::ACTIVITIES),
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static {
    return $this// ->afterInstantiate(function(Activity $activity): void {})
      ;
  }

  public static function class(): string {
    return Activity::class;
  }
}
