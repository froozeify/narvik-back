<?php

namespace App\Tests\Enum;

use Symfony\Component\HttpFoundation\Response;

enum ResponseCodeEnum: int {
  case created = Response::HTTP_CREATED;
  case ok = Response::HTTP_OK;
  case no_content = Response::HTTP_NO_CONTENT;

  case not_found = Response::HTTP_NOT_FOUND;
  case not_allowed = Response::HTTP_METHOD_NOT_ALLOWED;
  case bad_request = Response::HTTP_BAD_REQUEST;
  case forbidden = Response::HTTP_FORBIDDEN;
  case unprocessable_422 = Response::HTTP_UNPROCESSABLE_ENTITY;
  case locked_423 = Response::HTTP_LOCKED;

  public function isSuccess(): bool {
    return ($this->value >= 200 && $this->value <= 299);
  }
}
