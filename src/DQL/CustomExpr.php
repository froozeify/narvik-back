<?php

namespace App\DQL;

use Doctrine\ORM\Query\Expr;

class CustomExpr {
  public static function unaccent(string $x): string {
    return new Expr\Func('unaccent', [$x]);
  }

  public static function unaccentInsensitive(string $x): string {
    return self::unaccent(new Expr\Func('LOWER', [$x]));
  }
}
