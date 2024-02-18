<?php

namespace App\Factory;

use App\Entity\MemberSeason;
use App\Repository\MemberSeasonRepository;
use App\Story\SeasonStory;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<MemberSeason>
 *
 * @method        MemberSeason|Proxy                     create(array|callable $attributes = [])
 * @method static MemberSeason|Proxy                     createOne(array $attributes = [])
 * @method static MemberSeason|Proxy                     find(object|array|mixed $criteria)
 * @method static MemberSeason|Proxy                     findOrCreate(array $attributes)
 * @method static MemberSeason|Proxy                     first(string $sortedField = 'id')
 * @method static MemberSeason|Proxy                     last(string $sortedField = 'id')
 * @method static MemberSeason|Proxy                     random(array $attributes = [])
 * @method static MemberSeason|Proxy                     randomOrCreate(array $attributes = [])
 * @method static MemberSeasonRepository|RepositoryProxy repository()
 * @method static MemberSeason[]|Proxy[]                 all()
 * @method static MemberSeason[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static MemberSeason[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static MemberSeason[]|Proxy[]                 findBy(array $attributes)
 * @method static MemberSeason[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static MemberSeason[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class MemberSeasonFactory extends ModelFactory {
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
      'member'      => MemberFactory::randomOrCreate(),
      'ageCategory' => AgeCategoryFactory::randomOrCreate(),
//      'season'      => $season->object(), // Done in self::initialize()
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): self {
    return $this// ->afterInstantiate(function(MemberSeason $memberSeason): void {})
      ->afterInstantiate(function(MemberSeason $memberSeason): void {
        if (!$memberSeason->getSeason()) {
          $season = SeasonStory::getRandom('default_seasons');
          $memberSeason->setSeason($season->object());
        }
    })
      ;
  }

  protected static function getClass(): string {
    return MemberSeason::class;
  }
}
