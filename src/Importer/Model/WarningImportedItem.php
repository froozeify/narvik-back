<?php declare(strict_types=1);

namespace App\Importer\Model;

class WarningImportedItem extends AbstractImportedItemResult {
  private array $warnings = [];

  public function __construct(string $identifier, array $data = []) {
    parent::__construct(static::WARNING, $identifier, $data);
  }

  public function format(int $lineNumber): array {
    return array_merge(
      [
        "_warnings" => $this->warnings,
        "errorLine" => $lineNumber
      ],
      $this->data
    );
  }

  /**
   * @return array
   */
  public function getWarnings(): array {
    return $this->warnings;
  }

  /**
   * @param array $warnings
   * @return WarningImportedItem
   */
  public function setWarnings(array $warnings): WarningImportedItem {
    $this->warnings = $warnings;
    return $this;
  }

  public function addWarning(array $warning): WarningImportedItem {
    $this->warnings[] = $warning;
    return $this;
  }
}
