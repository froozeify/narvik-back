<?php

namespace App\Factory;

use App\Entity\Member;
use App\Enum\MemberRole;
use App\Repository\MemberRepository;
use App\Story\SeasonStory;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Member>
 *
 * @method        Member|Proxy                     create(array|callable $attributes = [])
 * @method static Member|Proxy                     createOne(array $attributes = [])
 * @method static Member|Proxy                     find(object|array|mixed $criteria)
 * @method static Member|Proxy                     findOrCreate(array $attributes)
 * @method static Member|Proxy                     first(string $sortedField = 'id')
 * @method static Member|Proxy                     last(string $sortedField = 'id')
 * @method static Member|Proxy                     random(array $attributes = [])
 * @method static Member|Proxy                     randomOrCreate(array $attributes = [])
 * @method static MemberRepository|RepositoryProxy repository()
 * @method static Member[]|Proxy[]                 all()
 * @method static Member[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Member[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Member[]|Proxy[]                 findBy(array $attributes)
 * @method static Member[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Member[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class MemberFactory extends ModelFactory {
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
      'firstname'   => self::faker()->firstName,
      'lastname'    => self::faker()->lastName,
      'email'       => self::faker()->unique()->safeEmail(),
      'licence'     => str_pad(self::faker()->numberBetween(1000000, 99999999), 8, "0", STR_PAD_LEFT),
      'role'        => MemberRole::user,
    ];
  }

  public function admin(string $email = null, string $password = null): self {
    return $this->addState([
      'licence'          => null,
      'firstname'        => 'admin',
      'lastname'         => 'admin',
      'email'            => $email ?? self::faker()->unique()->safeEmail(),
      'plainPassword'    => $password ?? 'admin',
      'accountActivated' => true,
      'role'             => MemberRole::admin,
    ]);
  }

  /**
   * Badger user is needed for login and register presence on the site
   * This special user is here so we can have the site publicly exposed
   * And protected behind a login page
   *
   * @return self
   */
  public function badger(): self {
    return $this->addState([
      'licence'          => null,
      'firstname'        => 'badger',
      'lastname'         => 'badger',
      'email'            => 'badger',
      'plainPassword'    => 'badger',
      'accountActivated' => false,
      'role'             => MemberRole::badger,
    ]);
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): self {
    return $this// ->afterInstantiate(function(User $user): void {})
      ;
  }

  protected static function getClass(): string {
    return Member::class;
  }
}
