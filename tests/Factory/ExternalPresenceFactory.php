<?php

namespace App\Tests\Factory;

use App\Entity\ClubDependent\Plugin\Presence\ExternalPresence;
use App\Repository\ExternalPresenceRepository;
use App\Tests\Story\_InitStory;
use App\Tests\Story\ActivityStory;
use DateTimeImmutable;
use Zenstruck\Foundry\FactoryCollection;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 *
 * @method ExternalPresence|Proxy create(array|callable $attributes = [])
 * @method static ExternalPresence|Proxy createOne(array $attributes = [])
 * @method static ExternalPresence|Proxy find(object|array|mixed $criteria)
 * @method static ExternalPresence|Proxy findOrCreate(array $attributes)
 * @method static ExternalPresence|Proxy first(string $sortedField = 'id')
 * @method static ExternalPresence|Proxy last(string $sortedField = 'id')
 * @method static ExternalPresence|Proxy random(array $attributes = [])
 * @method static ExternalPresence|Proxy randomOrCreate(array $attributes = [])
 * @method static ExternalPresence[]|Proxy[] all()
 * @method static ExternalPresence[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ExternalPresence[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static ExternalPresence[]|Proxy[] findBy(array $attributes)
 * @method static ExternalPresence[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ExternalPresence[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method FactoryCollection<ExternalPresence|Proxy> many(int $min, int|null $max = null)
 * @method FactoryCollection<ExternalPresence|Proxy> sequence(iterable|callable $sequence)
 * @method static ProxyRepositoryDecorator<ExternalPresence, ExternalPresenceRepository> repository()
 *
 * @phpstan-method \App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence> create(array|callable $attributes = [])
 * @phpstan-method static \App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence> createOne(array $attributes = [])
 * @phpstan-method static \App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence> find(object|array|mixed $criteria)
 * @phpstan-method static \App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence> findOrCreate(array $attributes)
 * @phpstan-method static \App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence> first(string $sortedField = 'id')
 * @phpstan-method static \App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence> last(string $sortedField = 'id')
 * @phpstan-method static \App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence> random(array $attributes = [])
 * @phpstan-method static \App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence>> all()
 * @phpstan-method static list<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence>> findBy(array $attributes)
 * @phpstan-method static list<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence>> randomSet(int $number, array $attributes = [])
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence>> many(int $min, int|null $max = null)
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\ClubDependent\Plugin\Presence\ExternalPresence>> sequence(iterable|callable $sequence)
 * @extends PersistentProxyObjectFactory<ExternalPresence>
 */
final class ExternalPresenceFactory extends PersistentProxyObjectFactory {
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
      'firstname'  => self::faker()->firstName(),
      'lastname'   => self::faker()->lastName(),
      'activities' => ActivityStory::getRandomRange('activities_club1', 1, 4),
      'club'       => _InitStory::club_1(),
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static {
    return $this// ->afterInstantiate(function(ExternalPresence $externalPresence): void {})
      ;
  }

  public static function class(): string {
    return ExternalPresence::class;
  }
}
