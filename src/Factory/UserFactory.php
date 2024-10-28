<?php

namespace App\Factory;

use App\Entity\User;
use App\Enum\UserRole;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<User>
 *
 * @method        User|Proxy                                create(array|callable $attributes = [])
 * @method static User|Proxy                                createOne(array $attributes = [])
 * @method static User|Proxy                                find(object|array|mixed $criteria)
 * @method static User|Proxy                                findOrCreate(array $attributes)
 * @method static User|Proxy                                first(string $sortedField = 'id')
 * @method static User|Proxy                                last(string $sortedField = 'id')
 * @method static User|Proxy                                random(array $attributes = [])
 * @method static User|Proxy                                randomOrCreate(array $attributes = [])
 * @method static UserRepository|ProxyRepositoryDecorator repository()
 * @method static User[]|Proxy[]                            all()
 * @method static User[]|Proxy[]                            createMany(int $number, array|callable $attributes = [])
 * @method static User[]|Proxy[]                            createSequence(iterable|callable $sequence)
 * @method static User[]|Proxy[]                            findBy(array $attributes)
 * @method static User[]|Proxy[]                            randomRange(int $min, int $max, array $attributes = [])
 * @method static User[]|Proxy[]                            randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        User&Proxy<User> create(array|callable $attributes = [])
 * @phpstan-method static User&Proxy<User> createOne(array $attributes = [])
 * @phpstan-method static User&Proxy<User> find(object|array|mixed $criteria)
 * @phpstan-method static User&Proxy<User> findOrCreate(array $attributes)
 * @phpstan-method static User&Proxy<User> first(string $sortedField = 'id')
 * @phpstan-method static User&Proxy<User> last(string $sortedField = 'id')
 * @phpstan-method static User&Proxy<User> random(array $attributes = [])
 * @phpstan-method static User&Proxy<User> randomOrCreate(array $attributes = [])
 * @phpstan-method static ProxyRepositoryDecorator<User, EntityRepository> repository()
 * @phpstan-method static list<User&Proxy<User>> all()
 * @phpstan-method static list<User&Proxy<User>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<User&Proxy<User>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<User&Proxy<User>> findBy(array $attributes)
 * @phpstan-method static list<User&Proxy<User>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<User&Proxy<User>> randomSet(int $number, array $attributes = [])
 */
final class UserFactory extends PersistentProxyObjectFactory {
  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
   */
  public function __construct() {
    parent::__construct();
  }

  public static function class(): string {
    return User::class;
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
   */
  protected function defaults(): array {
    return [
      'firstname' => self::faker()->firstName(),
      'lastname'  => self::faker()->lastName(),
      'email'     => self::faker()->unique()->safeEmail(),
      'roles'     => [UserRole::member->value],
    ];
  }

  public function superAdmin(string $email = null, string $password = null): self {
    return $this->with([
      'firstname'        => 'admin',
      'lastname'         => 'admin',
      'email'            => $email ?? self::faker()->unique()->safeEmail(),
      'plainPassword'    => $password ?? 'admin123',
      'accountActivated' => true,
      'roles'            => [UserRole::super_admin->value],
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
      'firstname'        => 'badger',
      'lastname'         => 'badger',
      'email'            => 'badger',
      'plainPassword'    => 'badger123',
      'accountActivated' => false,
      'roles'            => [UserRole::badger->value],
    ]);
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static {
    return $this// ->afterInstantiate(function(User $user): void {})
      ;
  }
}
