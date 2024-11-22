<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Enum\ClubRole;
use App\Enum\UserRole;
use App\Tests\Enum\ResponseCodeEnum;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class AbstractTestCase extends ApiTestCase {
  use ResetDatabase;
  use Factories;

  use CustomApiTestAssertionsTrait;

  private ?string $accessToken = null;
  private ?string $refreshToken = null;

  public function setUp(): void {
    parent::setUp();
    self::bootKernel();
    $this->initDefaultFixtures();
  }

  public function initDefaultFixtures(): void {}

  #[NoReturn]
  public function debugTestDatabase(): void {
    \DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver::commit();
    die; // The DB changes are actually persisted
  }

  protected function createClientWithCredentials(string $token = null): Client {
    $token = $token ?? $this->accessToken;

    return static::createClient([], [
      'headers' => [
        'Authorization' => 'Bearer ' . $token,
      ],
    ]);
  }

  protected function logout(): void {
    $this->accessToken = null;
    $this->refreshToken = null;
  }

  protected function loggedAs(string $email, string $password): bool {
    $this->logout();

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

  private function checkRequestResponse(ResponseCodeEnum $responseCode, ?array $payloadToValidate): void {
    $this->assertResponseStatusCodeSame($responseCode->value);

    if ($responseCode->isSuccess()) {
      if ($payloadToValidate) {
        $this->assertJsonContains($payloadToValidate);
      }
    }
  }

  public function makeAllLoggedRequests(
    ?array $payloadToValidate = null,
    ResponseCodeEnum $memberClub1Code = ResponseCodeEnum::forbidden,
    ResponseCodeEnum $supervisorClub1Code = ResponseCodeEnum::ok,
    ResponseCodeEnum $adminClub1Code  = ResponseCodeEnum::ok,
    ?ResponseCodeEnum $adminClub2Code = ResponseCodeEnum::not_found,
    ResponseCodeEnum $superAdminCode = ResponseCodeEnum::ok,
    ?\Closure $requestFunction = null
  ): void {
    // Super admin
    $this->loggedAsSuperAdmin();
    if ($requestFunction) $requestFunction(UserRole::super_admin->value, null);
    $this->checkRequestResponse($superAdminCode, $payloadToValidate);

    // Admin club 1
    $this->loggedAsAdminClub1();
    if ($requestFunction) $requestFunction(ClubRole::admin->value, 1);
    $this->checkRequestResponse($adminClub1Code, $payloadToValidate);

    // Supervisor club 1
    $this->loggedAsSupervisorClub1();
    if ($requestFunction) $requestFunction(ClubRole::supervisor->value, 1);
    $this->checkRequestResponse($supervisorClub1Code, $payloadToValidate);

    // member club 1
    $this->loggedAsMemberClub1();
    if ($requestFunction) $requestFunction(ClubRole::member->value, 1);
    $this->checkRequestResponse($memberClub1Code, $payloadToValidate);

    // Club 2
    if ($adminClub2Code) {
      // Admin club 2
      $this->loggedAsAdminClub2();
      if ($requestFunction) $requestFunction(ClubRole::admin->value, 2);
      $this->checkRequestResponse($adminClub2Code, $payloadToValidate);
    }
  }

  public function loggedAsSuperAdmin(): bool {
    return $this->loggedAs('admin@admin.com', 'admin123');
  }

  public function loggedAsAdminClub1(): bool {
    return $this->loggedAs('admin@club1.fr', 'admin123');
  }
  public function loggedAsAdminClub2(): bool {
    return $this->loggedAs('admin@club2.fr', 'admin123');
  }

  public function loggedAsSupervisorClub1(): bool {
    return $this->loggedAs('supervisor@club1.fr', 'admin123');
  }

  public function loggedAsMemberClub1(): bool {
    return $this->loggedAs('member@club1.fr', 'member123');
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

  public function makeLoggedRequest(string $method, string $url, ?array $data = null, array $uriParameters = [], array $queryOptions = []): ResponseInterface {
    $options = array_merge($queryOptions, $this->prepareRequestOptions($data, $uriParameters));
    $response = static::createClientWithCredentials()->request($method, $url, $options);
    return $response;
  }

  // CRUD Requests

  public function makeGetRequest(string $url, array $uriParameters = []): ResponseInterface {
    return $this->makeLoggedRequest(Request::METHOD_GET, $url, uriParameters: $uriParameters);
  }

  public function makePostRequest(string $url, array $data = null, array $uriParameters = []): ResponseInterface {
    return $this->makeLoggedRequest(Request::METHOD_POST, $url, $data, $uriParameters);
  }

  public function makePutRequest(string $url, array $data = null, array $uriParameters = []): ResponseInterface {
    return $this->makeLoggedRequest(Request::METHOD_PUT, $url, $data, $uriParameters);
  }

  public function makePatchRequest(string $url, array $data = null, array $uriParameters = []): ResponseInterface {
    return $this->makeLoggedRequest(Request::METHOD_PATCH, $url, $data, $uriParameters, ['headers' => ['Content-Type' => 'application/merge-patch+json']]);
  }

  public function makeDeleteRequest(string $url, array $data = null, array $uriParameters = []): ResponseInterface {
    return $this->makeLoggedRequest(Request::METHOD_DELETE, $url, $data, $uriParameters);
  }

}
