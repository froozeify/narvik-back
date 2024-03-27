<?php declare(strict_types=1);

namespace App\Importer\Model;

class ErrorImportedItem extends AbstractImportedItemResult {

  public function __construct(string $identifier, array $data = []) {
    parent::__construct(static::ERROR, $identifier, $data);
  }

  public function format(int $lineNumber): array {
    return array_merge(
      ["errorLine" => $lineNumber],
      $this->data
    );
  }
}
