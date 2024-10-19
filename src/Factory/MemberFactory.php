<?php

namespace App\Factory;

use App\Entity\Member;
use App\Enum\MemberRole;
use App\Repository\MemberRepository;
use Zenstruck\Foundry\FactoryCollection;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 *
 * @method Member|Proxy create(array|callable $attributes = [])
 * @method static Member|Proxy createOne(array $attributes = [])
 * @method static Member|Proxy find(object|array|mixed $criteria)
 * @method static Member|Proxy findOrCreate(array $attributes)
 * @method static Member|Proxy first(string $sortedField = 'id')
 * @method static Member|Proxy last(string $sortedField = 'id')
 * @method static Member|Proxy random(array $attributes = [])
 * @method static Member|Proxy randomOrCreate(array $attributes = [])
 * @method static Member[]|Proxy[] all()
 * @method static Member[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Member[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Member[]|Proxy[] findBy(array $attributes)
 * @method static Member[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Member[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method FactoryCollection<Member|Proxy> many(int $min, int|null $max = null)
 * @method FactoryCollection<Member|Proxy> sequence(iterable|callable $sequence)
 * @method static ProxyRepositoryDecorator<Member, MemberRepository> repository()
 *
 * @phpstan-method \App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member> create(array|callable $attributes = [])
 * @phpstan-method static \App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member> createOne(array $attributes = [])
 * @phpstan-method static \App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member> find(object|array|mixed $criteria)
 * @phpstan-method static \App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member> findOrCreate(array $attributes)
 * @phpstan-method static \App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member> first(string $sortedField = 'id')
 * @phpstan-method static \App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member> last(string $sortedField = 'id')
 * @phpstan-method static \App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member> random(array $attributes = [])
 * @phpstan-method static \App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<\App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member>> all()
 * @phpstan-method static list<\App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<\App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<\App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member>> findBy(array $attributes)
 * @phpstan-method static list<\App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<\App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member>> randomSet(int $number, array $attributes = [])
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member>> many(int $min, int|null $max = null)
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Entity\Member&\Zenstruck\Foundry\Persistence\Proxy<\App\Entity\Member>> sequence(iterable|callable $sequence)
 * @extends PersistentProxyObjectFactory<Member>
 */
final class MemberFactory extends PersistentProxyObjectFactory {
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
      'firstname' => self::faker()->firstName(),
      'lastname'  => self::faker()->lastName(),
      'email'     => self::faker()->unique()->safeEmail(),
      'licence'   => str_pad(self::faker()->numberBetween(1000000, 99999999), 8, "0", STR_PAD_LEFT),
      'role'      => MemberRole::user,
    ];
  }

  public function admin(string $email = null, string $password = null): self {
    return $this->with([
      'licence'          => null,
      'firstname'        => 'admin',
      'lastname'         => 'admin',
      'email'            => $email ?? self::faker()->unique()->safeEmail(),
      'plainPassword'    => $password ?? 'admin123',
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
    return $this->with([
      'licence'          => null,
      'firstname'        => 'badger',
      'lastname'         => 'badger',
      'email'            => 'badger',
      'plainPassword'    => 'badger123',
      'accountActivated' => false,
      'role'             => MemberRole::badger,
    ]);
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static {
    return $this// ->afterInstantiate(function(User $user): void {})
      ;
  }

  public static function class(): string {
    return Member::class;
  }
}
