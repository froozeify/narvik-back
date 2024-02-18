<?php

namespace App\Factory;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Activity>
 *
 * @method        Activity|Proxy                     create(array|callable $attributes = [])
 * @method static Activity|Proxy                     createOne(array $attributes = [])
 * @method static Activity|Proxy                     find(object|array|mixed $criteria)
 * @method static Activity|Proxy                     findOrCreate(array $attributes)
 * @method static Activity|Proxy                     first(string $sortedField = 'id')
 * @method static Activity|Proxy                     last(string $sortedField = 'id')
 * @method static Activity|Proxy                     random(array $attributes = [])
 * @method static Activity|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ActivityRepository|RepositoryProxy repository()
 * @method static Activity[]|Proxy[]                 all()
 * @method static Activity[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Activity[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Activity[]|Proxy[]                 findBy(array $attributes)
 * @method static Activity[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Activity[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class ActivityFactory extends ModelFactory {
  const ACTIVITIES = [
    '10M',
    '25M rameneurs',
    '25M toutes armes',
    '50M rameneurs',
    '50M 22Hunter',
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
  protected function getDefaults(): array {
    return [
      'name' => self::faker()->randomElement(self::ACTIVITIES),
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): self {
    return $this// ->afterInstantiate(function(Activity $activity): void {})
      ;
  }

  protected static function getClass(): string {
    return Activity::class;
  }
}
