<?php

namespace App\Tests\Factory;

use App\Entity\ClubDependent\MemberPresence;
use App\Repository\MemberPresenceRepository;
use App\Tests\Story\ActivityStory;
use DateTimeImmutable;
use Zenstruck\Foundry\FactoryCollection;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 *
 * @method MemberPresence|Proxy create(array|callable $attributes = [])
 * @method static MemberPresence|Proxy createOne(array $attributes = [])
 * @method static MemberPresence|Proxy find(object|array|mixed $criteria)
 * @method static MemberPresence|Proxy findOrCreate(array $attributes)
 * @method static MemberPresence|Proxy first(string $sortedField = 'id')
 * @method static MemberPresence|Proxy last(string $sortedField = 'id')
 * @method static MemberPresence|Proxy random(array $attributes = [])
 * @method static MemberPresence|Proxy randomOrCreate(array $attributes = [])
 * @method static MemberPresence[]|Proxy[] all()
 * @method static MemberPresence[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static MemberPresence[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static MemberPresence[]|Proxy[] findBy(array $attributes)
 * @method static MemberPresence[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static MemberPresence[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method FactoryCollection<MemberPresence|Proxy> many(int $min, int|null $max = null)
 * @method FactoryCollection<MemberPresence|Proxy> sequence(iterable|callable $sequence)
 * @method static ProxyRepositoryDecorator<MemberPresence, MemberPresenceRepository> repository()
 *
 * @phpstan-method \App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence> create(array|callable $attributes = [])
 * @phpstan-method static \App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence> createOne(array $attributes = [])
 * @phpstan-method static \App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence> find(object|array|mixed $criteria)
 * @phpstan-method static \App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence> findOrCreate(array $attributes)
 * @phpstan-method static \App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence> first(string $sortedField = 'id')
 * @phpstan-method static \App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence> last(string $sortedField = 'id')
 * @phpstan-method static \App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence> random(array $attributes = [])
 * @phpstan-method static \App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<\App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence>> all()
 * @phpstan-method static list<\App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<\App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<\App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence>> findBy(array $attributes)
 * @phpstan-method static list<\App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<\App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence>> randomSet(int $number, array $attributes = [])
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence>> many(int $min, int|null $max = null)
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Entity\ClubDependent\MemberPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\MemberPresence>> sequence(iterable|callable $sequence)
 * @extends PersistentProxyObjectFactory<MemberPresence>
 */
final class MemberPresenceFactory extends PersistentProxyObjectFactory {
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
      'date'       => DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-10 days', 'now')),
      'member'     => MemberFactory::randomOrCreate(),
      'activities' => ActivityStory::getRandomRange('activities_club1', 1, 4),
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static {
    return $this// ->afterInstantiate(function(MemberPresence $memberPresence): void {})
      ;
  }

  public static function class(): string {
    return MemberPresence::class;
  }
}
