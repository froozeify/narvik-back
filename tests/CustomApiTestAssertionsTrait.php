<?php

namespace App\Tests;

trait CustomApiTestAssertionsTrait {
  public static function assertJsonNotHasKey(string $key): void {
    static::assertArrayNotHasKey($key, self::getHttpResponse()->toArray(false));
  }
}
