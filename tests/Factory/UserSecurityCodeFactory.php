<?php

namespace App\Tests\Factory;

use App\Entity\UserSecurityCode;
use App\Enum\UserSecurityCodeTrigger;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<UserSecurityCode>
 */
final class UserSecurityCodeFactory extends PersistentProxyObjectFactory {
  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
   *
   */
  public function __construct() {
  }

  public static function class(): string {
    return UserSecurityCode::class;
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
   *
   */
  protected function defaults(): array|callable {
    return [
      'trigger' => self::faker()->randomElement(UserSecurityCodeTrigger::cases()),
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static {
    return $this// ->afterInstantiate(function(UserSecurityCode $userSecurityCode): void {})
      ;
  }
}
