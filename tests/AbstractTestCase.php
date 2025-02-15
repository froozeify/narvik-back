<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Club;
use App\Enum\ClubRole;
use App\Enum\UserRole;
use App\Tests\Enum\ResponseCodeEnum;
use App\Tests\Story\_InitStory;
use Doctrine\DBAL\Connection;
use JetBrains\PhpStorm\NoReturn;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class AbstractTestCase extends ApiTestCase {
  use ResetDatabase;
  use Factories;

  use CustomApiTestAssertionsTrait;

  private string $clientAuthorization = 'dGVzdDpzZWNyZXRUZXN0T25seQ=='; // base64 encode test:secretTestOnly

  private ?string $accessToken = null;
  private ?string $refreshToken = null;
  private ?string $selectedProfile = null;

  public function setUp(): void {
    parent::setUp();
    self::bootKernel();
    $registry = self::$kernel->getContainer()->get('doctrine');
    /** @var Connection $connection */
    $connection = $registry->getConnection();
    $connection->executeQuery('CREATE EXTENSION unaccent;');

    $this->initDefaultFixtures();
  }

  public function tearDown(): void {
    $fs = self::getContainer()->get(FileSystem::class);
    $testFolder = self::$kernel->getContainer()->getParameter('app.files');
    if ($fs->exists($testFolder)) {
      $fs->remove($testFolder);
    }

    parent::tearDown();
  }


  public function initDefaultFixtures(): void {}

  #[NoReturn]
  public function debugTestDatabase(): never {
    \DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver::commit();
    die; // The DB changes are actually persisted
  }

  protected function createClientWithCredentials(?string $token = null): Client {
    $token = $token ?? $this->accessToken;

    $headers = [
      'Authorization' => 'Bearer ' . $token,
    ];
    if ($this->selectedProfile) {
      $headers['Profile'] = $this->selectedProfile;
    }

    return static::createClient([], [
      'headers' => $headers,
    ]);
  }

  protected function logout(): void {
    $this->accessToken = null;
    $this->refreshToken = null;
    $this->selectedProfile = null;
  }

  protected function loggedAs(string $email, string $password): bool {
    $this->logout();

    $response = static::createClient()->request(Request::METHOD_POST, '/token', [
      'json' => [
        'grant_type' => 'password',
        'username' => $email,
        'password' => $password,
      ],
      'headers' => [
        'Authorization' => 'Basic ' . $this->clientAuthorization,
      ]
    ]);

    if ($response->getStatusCode() !== Response::HTTP_OK) {
      return false;
    }

    $data = $response->toArray();
    $this->accessToken = $data['token'];
    $this->refreshToken = $data['refresh_token'];
    return true;
  }

  protected function loggedAsBadger(Club $club): bool {
    $this->logout();
    $response = static::createClient()->request(Request::METHOD_POST, '/auth/bdg', [
      'json' => [
        'token' => $club->getBadgerToken(),
        'club' => $club->getUuid()->toString(),
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

  /**
   * Possibility to define in the query headers the selectedProfile.
   * Required when the user have multiple profile
   *
   * @param string|null $id
   *
   * @return void
   */
  public function selectedProfile(?string $id): void {
    $this->selectedProfile = $id;
  }

  private function checkRequestResponse(ResponseCodeEnum $responseCode, ?array $payloadToValidate, string $context): void {
    try {
      $this->assertResponseStatusCodeSame($responseCode->value);

      if ($responseCode->isSuccess()) {
        if ($payloadToValidate) {
          $this->assertJsonContains($payloadToValidate);
        }
      }
    } catch (ExpectationFailedException $exception) {
      throw new ExpectationFailedException("CheckRequestResponse context: " . $context . "\n" . $exception->getMessage(), $exception->getComparisonFailure(), $exception->getPrevious());
    }
  }

  public function makeAllLoggedRequests(
    ?array &$payloadToValidate = null,
    ResponseCodeEnum $memberClub1Code = ResponseCodeEnum::forbidden,
    ResponseCodeEnum $supervisorClub1Code = ResponseCodeEnum::ok,
    ResponseCodeEnum $adminClub1Code  = ResponseCodeEnum::ok,
    ?ResponseCodeEnum $adminClub2Code = ResponseCodeEnum::not_found,
    ResponseCodeEnum $superAdminCode = ResponseCodeEnum::ok,
    ResponseCodeEnum $badgerClub1Code = ResponseCodeEnum::forbidden,
    ?ResponseCodeEnum $badgerClub2Code = ResponseCodeEnum::not_found,
    ?\Closure $requestFunction = null,
  ): void {
    // Super admin
    $this->loggedAsSuperAdmin();
    if ($requestFunction) $requestFunction(UserRole::super_admin->value, null);
    $this->checkRequestResponse($superAdminCode, $payloadToValidate, "Super admin");

    // Admin club 1
    $this->loggedAsAdminClub1();
    if ($requestFunction) $requestFunction(ClubRole::admin->value, 1);
    $this->checkRequestResponse($adminClub1Code, $payloadToValidate, "Admin club 1");

    // Supervisor club 1
    $this->loggedAsSupervisorClub1();
    if ($requestFunction) $requestFunction(ClubRole::supervisor->value, 1);
    $this->checkRequestResponse($supervisorClub1Code, $payloadToValidate, "Supervisor club 1");

    // member club 1
    $this->loggedAsMemberClub1();
    if ($requestFunction) $requestFunction(ClubRole::member->value, 1);
    $this->checkRequestResponse($memberClub1Code, $payloadToValidate, "Member club 1");

    // Badger club 1
    $this->loggedAsBadgerClub1();
    if ($requestFunction) $requestFunction(ClubRole::badger->value, 1);
    $this->checkRequestResponse($badgerClub1Code, $payloadToValidate, "Badger club 1");

    // Club 2
    if ($adminClub2Code) {
      // Admin club 2
      $this->loggedAsAdminClub2();
      if ($requestFunction) $requestFunction(ClubRole::admin->value, 2);
      $this->checkRequestResponse($adminClub2Code, $payloadToValidate, "Admin club 2");

      // Badger club 2
      $this->loggedAsBadgerClub2();
      if ($requestFunction) $requestFunction(ClubRole::badger->value, 2);
      $this->checkRequestResponse($badgerClub2Code, $payloadToValidate, "Badger club 2");
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

  public function loggedAsMemberClub2(): bool {
    return $this->loggedAs('member@club2.fr', 'member123');
  }

  public function loggedAsBadgerClub1(): bool {
    return $this->loggedAsBadger(_InitStory::club_1());
  }

  public function loggedAsBadgerClub2(): bool {
    return $this->loggedAsBadger(_InitStory::club_2());
  }

  private function prepareRequestOptions(?array $data = null, array $uriParameters = []): array {
    $options = [];

    if (!is_null($data)) {
      if (!array_key_exists('_not_json', $data)) {
        $options['json'] = $data;
      } else {
        unset($data['_not_json']);
        $options = $data;
      }
    }

    if (!empty($uriParameters)) {
      $options['extra']['parameters'] = $uriParameters;
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

  public function makeGetCsvRequest(string $url): ResponseInterface {
    return $this->makeLoggedRequest(Request::METHOD_GET, $url, queryOptions: ['headers' => ['accept' => ['text/csv']]]);
  }

  public function makePostRequest(string $url, ?array $data = null, array $uriParameters = []): ResponseInterface {
    return $this->makeLoggedRequest(Request::METHOD_POST, $url, $data, $uriParameters);
  }

  public function makePutRequest(string $url, ?array $data = null, array $uriParameters = []): ResponseInterface {
    return $this->makeLoggedRequest(Request::METHOD_PUT, $url, $data, $uriParameters);
  }

  public function makePatchRequest(string $url, ?array $data = null, array $uriParameters = []): ResponseInterface {
    return $this->makeLoggedRequest(Request::METHOD_PATCH, $url, $data, $uriParameters, ['headers' => ['Content-Type' => 'application/merge-patch+json']]);
  }

  public function makeDeleteRequest(string $url, ?array $data = null, array $uriParameters = []): ResponseInterface {
    return $this->makeLoggedRequest(Request::METHOD_DELETE, $url, $data, $uriParameters);
  }

}
