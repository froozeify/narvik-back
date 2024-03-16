<?php

namespace App\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Unaccent string using postgresql extension unaccent :
 * http://www.postgresql.org/docs/current/static/unaccent.html
 *
 * Usage : StringFunction UNACCENT(string)
 *
 */
class UnaccentString extends FunctionNode {
  private $subselect;

  public function getSql(SqlWalker $sqlWalker) {
    return 'UNACCENT(' . $this->subselect->dispatch($sqlWalker) . ')';
  }

  public function parse(Parser $parser) {
    $parser->match(Lexer::T_IDENTIFIER);
    $parser->match(Lexer::T_OPEN_PARENTHESIS);
    $this->subselect = $parser->StringPrimary();
    $parser->match(Lexer::T_CLOSE_PARENTHESIS);
  }
}
