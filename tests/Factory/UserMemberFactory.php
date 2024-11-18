<?php

namespace App\Tests\Factory;

use App\Entity\UserMember;
use App\Enum\ClubRole;
use App\Repository\UserMemberRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<UserMember>
 *
 * @method        UserMember|Proxy                              create(array|callable $attributes = [])
 * @method static UserMember|Proxy                              createOne(array $attributes = [])
 * @method static UserMember|Proxy                              find(object|array|mixed $criteria)
 * @method static UserMember|Proxy                              findOrCreate(array $attributes)
 * @method static UserMember|Proxy                              first(string $sortedField = 'id')
 * @method static UserMember|Proxy                              last(string $sortedField = 'id')
 * @method static UserMember|Proxy                              random(array $attributes = [])
 * @method static UserMember|Proxy                              randomOrCreate(array $attributes = [])
 * @method static UserMemberRepository|ProxyRepositoryDecorator repository()
 * @method static UserMember[]|Proxy[]                          all()
 * @method static UserMember[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static UserMember[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static UserMember[]|Proxy[]                          findBy(array $attributes)
 * @method static UserMember[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static UserMember[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        UserMember&Proxy<UserMember> create(array|callable $attributes = [])
 * @phpstan-method static UserMember&Proxy<UserMember> createOne(array $attributes = [])
 * @phpstan-method static UserMember&Proxy<UserMember> find(object|array|mixed $criteria)
 * @phpstan-method static UserMember&Proxy<UserMember> findOrCreate(array $attributes)
 * @phpstan-method static UserMember&Proxy<UserMember> first(string $sortedField = 'id')
 * @phpstan-method static UserMember&Proxy<UserMember> last(string $sortedField = 'id')
 * @phpstan-method static UserMember&Proxy<UserMember> random(array $attributes = [])
 * @phpstan-method static UserMember&Proxy<UserMember> randomOrCreate(array $attributes = [])
 * @phpstan-method static ProxyRepositoryDecorator<UserMember, EntityRepository> repository()
 * @phpstan-method static list<UserMember&Proxy<UserMember>> all()
 * @phpstan-method static list<UserMember&Proxy<UserMember>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<UserMember&Proxy<UserMember>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<UserMember&Proxy<UserMember>> findBy(array $attributes)
 * @phpstan-method static list<UserMember&Proxy<UserMember>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<UserMember&Proxy<UserMember>> randomSet(int $number, array $attributes = [])
 */
final class UserMemberFactory extends PersistentProxyObjectFactory {
  /**
   * @see  https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
   *
   */
  public function __construct() {
  }

  public static function class(): string {
    return UserMember::class;
  }

  /**
   * @see  https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
   *
   */
  protected function defaults(): array|callable {
    return [
      // 'member' => MemberFactory::random(), // Member must be defined since we can't have it multiple time (OneToOne relationship)
      'user'   => UserFactory::random(),
      'role'   => self::faker()->randomElement(ClubRole::cases()),
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static {
    return $this// ->afterInstantiate(function(UserMember $userMember): void {})
      ;
  }
}
