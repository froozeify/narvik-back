<?php

namespace App\Tests;

use Symfony\Contracts\HttpClient\ResponseInterface;

trait CustomApiTestAssertionsTrait {
  public static function assertJsonNotHasKey(string $key, ResponseInterface $response): void {
    static::assertArrayNotHasKey($key, $response->toArray(false));
  }

  public static function assertJsonHasKey(string $key, ResponseInterface $response): void {
    static::assertArrayHasKey($key, $response->toArray(false));
  }
}
