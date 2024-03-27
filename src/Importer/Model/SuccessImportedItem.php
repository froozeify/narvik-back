<?php declare(strict_types=1);

namespace App\Importer\Model;

class SuccessImportedItem extends AbstractImportedItemResult {

  public function __construct(array $data = []) {
    parent::__construct(static::SUCCESS, "", $data);
  }

  public function format(int $lineNumber): array {
    return $this->data;
  }
}
