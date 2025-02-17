<?php

namespace App\Service;

class UtilsService {

  public static function convertStringToDbDecimal(?string $string): ?string {
    if (!is_numeric($string) && empty($string)) {
      return null;
    }
    return filter_var(str_replace(',', '.', $string), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
  }

  public function generateRandomToken(int $length): string {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-';
    $charLength = strlen($characters) - 1;
    $result = '';
    for ($i = 0; $i < $length; $i++) {
      $result .= $characters[mt_rand(0, $charLength)];
    }
    return $result;
  }
}
