<?php

namespace App\Factory;

use App\Entity\ExternalPresence;
use App\Repository\ExternalPresenceRepository;
use App\Story\ActivityStory;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<ExternalPresence>
 *
 * @method        ExternalPresence|Proxy                     create(array|callable $attributes = [])
 * @method static ExternalPresence|Proxy                     createOne(array $attributes = [])
 * @method static ExternalPresence|Proxy                     find(object|array|mixed $criteria)
 * @method static ExternalPresence|Proxy                     findOrCreate(array $attributes)
 * @method static ExternalPresence|Proxy                     first(string $sortedField = 'id')
 * @method static ExternalPresence|Proxy                     last(string $sortedField = 'id')
 * @method static ExternalPresence|Proxy                     random(array $attributes = [])
 * @method static ExternalPresence|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ExternalPresenceRepository|RepositoryProxy repository()
 * @method static ExternalPresence[]|Proxy[]                 all()
 * @method static ExternalPresence[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static ExternalPresence[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static ExternalPresence[]|Proxy[]                 findBy(array $attributes)
 * @method static ExternalPresence[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static ExternalPresence[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class ExternalPresenceFactory extends ModelFactory {
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
      'date'      => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-10 days', 'now')),
      'firstname' => self::faker()->firstName,
      'lastname'  => self::faker()->lastName,
      'activities' => ActivityStory::getRandomRange('default_activities', 1, 4)
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): self {
    return $this// ->afterInstantiate(function(ExternalPresence $externalPresence): void {})
      ;
  }

  protected static function getClass(): string {
    return ExternalPresence::class;
  }
}
