<?php

namespace App\Factory;

use App\Entity\MemberSeason;
use App\Repository\MemberSeasonRepository;
use App\Story\SeasonStory;
use Zenstruck\Foundry\FactoryCollection;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 *
 * @method MemberSeason|Proxy create(array|callable $attributes = [])
 * @method static MemberSeason|Proxy createOne(array $attributes = [])
 * @method static MemberSeason|Proxy find(object|array|mixed $criteria)
 * @method static MemberSeason|Proxy findOrCreate(array $attributes)
 * @method static MemberSeason|Proxy first(string $sortedField = 'id')
 * @method static MemberSeason|Proxy last(string $sortedField = 'id')
 * @method static MemberSeason|Proxy random(array $attributes = [])
 * @method static MemberSeason|Proxy randomOrCreate(array $attributes = [])
 * @method static MemberSeason[]|Proxy[] all()
 * @method static MemberSeason[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static MemberSeason[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static MemberSeason[]|Proxy[] findBy(array $attributes)
 * @method static MemberSeason[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static MemberSeason[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method FactoryCollection<MemberSeason|Proxy> many(int $min, int|null $max = null)
 * @method FactoryCollection<MemberSeason|Proxy> sequence(iterable|callable $sequence)
 * @method static ProxyRepositoryDecorator<MemberSeason, MemberSeasonRepository> repository()
 *
 * @phpstan-method \App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason> create(array|callable $attributes = [])
 * @phpstan-method static \App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason> createOne(array $attributes = [])
 * @phpstan-method static \App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason> find(object|array|mixed $criteria)
 * @phpstan-method static \App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason> findOrCreate(array $attributes)
 * @phpstan-method static \App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason> first(string $sortedField = 'id')
 * @phpstan-method static \App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason> last(string $sortedField = 'id')
 * @phpstan-method static \App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason> random(array $attributes = [])
 * @phpstan-method static \App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<\App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason>> all()
 * @phpstan-method static list<\App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<\App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<\App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason>> findBy(array $attributes)
 * @phpstan-method static list<\App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<\App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason>> randomSet(int $number, array $attributes = [])
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason>> many(int $min, int|null $max = null)
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Entity\MemberSeason&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\MemberSeason>> sequence(iterable|callable $sequence)
 * @extends PersistentProxyObjectFactory<MemberSeason>
 */
final class MemberSeasonFactory extends PersistentProxyObjectFactory {
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
      'member'      => MemberFactory::randomOrCreate(),
      'ageCategory' => AgeCategoryFactory::randomOrCreate(),
      //      'season'      => $season->object(), // Done in self::initialize()
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static {
    return $this// ->afterInstantiate(function(MemberSeason $memberSeason): void {})
    ->afterInstantiate(function(MemberSeason $memberSeason): void {
      if (!$memberSeason->getSeason()) {
        $season = SeasonStory::getRandom('default_seasons');
        $memberSeason->setSeason($season->_real());
      }
    });
  }

  public static function class(): string {
    return MemberSeason::class;
  }
}
