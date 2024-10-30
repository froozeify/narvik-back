<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class AbstractTest extends ApiTestCase {
  use ResetDatabase;
  use Factories;

  private ?string $accessToken = null;
  private ?string $refreshToken = null;

  public function setUp(): void {
    self::bootKernel();
  }

  protected function createClientWithCredentials(string $token = null): Client {
    $token = $token ?? $this->accessToken;

    return static::createClient([], [
      'headers' => [
        'Authorization' => 'Bearer ' . $token,
      ],
    ]);
  }

  protected function login(string $email, string $password): ?string {
    $response = static::createClient()->request('POST', '/auth', [
      'json' => [
        'email' => $email,
        'password' => $password,
      ]
    ]);

    $this->assertResponseIsSuccessful();
    $data = $response->toArray();
    $this->accessToken = $data['token'];
    $this->refreshToken = $data['refresh_token'];
    return $this->accessToken;
  }

  public function loginAsSuperAdmin(): ?string {
    return $this->login('admin@admin.com', 'admin123');
  }

}
