<?php

namespace App\Factory;

use App\Entity\MemberPresence;
use App\Repository\MemberPresenceRepository;
use App\Story\ActivityStory;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<MemberPresence>
 *
 * @method        MemberPresence|Proxy                     create(array|callable $attributes = [])
 * @method static MemberPresence|Proxy                     createOne(array $attributes = [])
 * @method static MemberPresence|Proxy                     find(object|array|mixed $criteria)
 * @method static MemberPresence|Proxy                     findOrCreate(array $attributes)
 * @method static MemberPresence|Proxy                     first(string $sortedField = 'id')
 * @method static MemberPresence|Proxy                     last(string $sortedField = 'id')
 * @method static MemberPresence|Proxy                     random(array $attributes = [])
 * @method static MemberPresence|Proxy                     randomOrCreate(array $attributes = [])
 * @method static MemberPresenceRepository|RepositoryProxy repository()
 * @method static MemberPresence[]|Proxy[]                 all()
 * @method static MemberPresence[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static MemberPresence[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static MemberPresence[]|Proxy[]                 findBy(array $attributes)
 * @method static MemberPresence[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static MemberPresence[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class MemberPresenceFactory extends ModelFactory {
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
      'date'   => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-10 days', 'now')),
      'member' => MemberFactory::randomOrCreate(),
      'activities' => ActivityStory::getRandomRange('default_activities', 1, 4)
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): self {
    return $this// ->afterInstantiate(function(MemberPresence $memberPresence): void {})
      ;
  }

  protected static function getClass(): string {
    return MemberPresence::class;
  }
}
