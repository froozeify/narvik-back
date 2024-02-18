<?php

namespace App\Factory;

use App\Entity\AgeCategory;
use App\Repository\AgeCategoryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<AgeCategory>
 *
 * @method        AgeCategory|Proxy                     create(array|callable $attributes = [])
 * @method static AgeCategory|Proxy                     createOne(array $attributes = [])
 * @method static AgeCategory|Proxy                     find(object|array|mixed $criteria)
 * @method static AgeCategory|Proxy                     findOrCreate(array $attributes)
 * @method static AgeCategory|Proxy                     first(string $sortedField = 'id')
 * @method static AgeCategory|Proxy                     last(string $sortedField = 'id')
 * @method static AgeCategory|Proxy                     random(array $attributes = [])
 * @method static AgeCategory|Proxy                     randomOrCreate(array $attributes = [])
 * @method static AgeCategoryRepository|RepositoryProxy repository()
 * @method static AgeCategory[]|Proxy[]                 all()
 * @method static AgeCategory[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static AgeCategory[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static AgeCategory[]|Proxy[]                 findBy(array $attributes)
 * @method static AgeCategory[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static AgeCategory[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class AgeCategoryFactory extends ModelFactory {
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
      'code' => 'S1',
      'name' => 'Senior 1',
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): self {
    return $this// ->afterInstantiate(function(AgeCategory $ageCategory): void {})
      ;
  }

  protected static function getClass(): string {
    return AgeCategory::class;
  }
}
