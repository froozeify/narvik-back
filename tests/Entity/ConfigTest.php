<?php

namespace App\Tests\Entity;

use App\Tests\AbstractTestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ConfigTest extends AbstractTestCase {
  private const string URL = "/public/config";

  public function testConfigAsSuperAdmin(): void {
    $this->loggedAsSuperAdmin();
    $response = $this->makeGetRequest(self::URL);
    $this->assertResponseIsSuccessful();
    $this->assertJsonHasKey("appVersion", $response);
  }

  private function getConfigAsPublic(): void {
    $response = $this->makeGetRequest(self::URL);
    $this->assertResponseIsSuccessful();
    $this->assertJsonNotHasKey("appVersion", $response);
  }

  public function testConfigAsUser(): void {
    $this->loggedAsMemberClub1();
    $this->getConfigAsPublic();
    $this->assertJsonContains(["id" => "user"]);
  }

  public function testConfigAsPublic(): void {
    $this->logout();
    $this->getConfigAsPublic();
    $this->assertJsonContains(["id" => "default"]);
  }
}
