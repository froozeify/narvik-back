<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;
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

  protected function loggedAs(string $email, string $password): bool {
    $response = static::createClient()->request(Request::METHOD_POST, '/auth', [
      'json' => [
        'email' => $email,
        'password' => $password,
      ],
    ]);

    if ($response->getStatusCode() !== Response::HTTP_OK) {
      return false;
    }

    $data = $response->toArray();
    $this->accessToken = $data['token'];
    $this->refreshToken = $data['refresh_token'];
    return true;
  }

  public function loggedAsSuperAdmin(): bool {
    return $this->loggedAs('admin@admin.com', 'admin123');
  }

  private function prepareRequestOptions(?array $data = null, array $uriParameters = []): array {
    $options = [];

    if (!empty($uriParameters)) {
      $options['extra']['parameters'] = $uriParameters;
    }

    if (!empty($data)) {
      $options['json'] = $data;
    }
    return $options;
  }

  public function makeNotLoggedRequest(string $method, string $url, ?array $data = null, array $uriParameters = []): ResponseInterface {
    $options = $this->prepareRequestOptions($data, $uriParameters);
    $response = static::createClient()->request($method, $url, $options);
    return $response;
  }

  public function makeLoggedRequest(string $method, string $url, ?array $data = null, array $uriParameters = []): ResponseInterface {
    $options = $this->prepareRequestOptions($data, $uriParameters);
    $response = static::createClientWithCredentials()->request($method, $url, $options);
    return $response;
  }

}
