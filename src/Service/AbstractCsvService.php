<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class AbstractCsvService {

  protected function convert(string $string): string {
    $encoding = mb_detect_encoding($string);
    if ($encoding === 'UTF-8') { // Nothing special to do
      return $string;
    } else if ($encoding === 'ASCII') {
      return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
    } else {
      throw new HttpException(Response::HTTP_BAD_REQUEST, "Unsupported CSV encoding : '$encoding'");
    }
  }
}
