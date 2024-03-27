<?php declare(strict_types=1);

namespace App\Importer\Model;

abstract class AbstractImportedItemResult {
  public const SUCCESS = "new";
  public const WARNING = "warning";
  public const ERROR = "error";

  protected string $status = self::ERROR;
  protected string $identifier = "";
  protected array $data = [];

  public function __construct(string $status, string $identifier, array $data = []) {
    $this->status = $status;
    $this->data = $data;
    $this->identifier = $identifier;
    if (!empty($identifier)) $this->data["identifier"] = $identifier;
  }

  /**
   * Generate a formatted standardised array response
   * This method is use in CsvImporter::updateResultFromAddItemResult
   *
   * @param int $lineNumber
   * @return array
   */
  abstract public function format(int $lineNumber): array;

  /**
   * @return string
   */
  public function getStatus(): string {
    return $this->status;
  }

  /**
   * @param string $status
   * @return AbstractImportedItemResult
   */
  public function setStatus(string $status): AbstractImportedItemResult {
    $this->status = $status;
    return $this;
  }

  /**
   * @return array
   */
  public function getData(): array {
    return $this->data;
  }

  /**
   * @param array $data
   * @return AbstractImportedItemResult
   */
  public function setData(array $data): AbstractImportedItemResult {
    $this->data = $data;
    return $this;
  }

  /**
   * @param string $name
   * @param array|string $value
   * @return $this
   */
  public function addData(string $name, array|string $value): AbstractImportedItemResult {
    $this->data[$name] = $value;
    return $this;
  }

  public function addDatas(array $datas): AbstractImportedItemResult {
    $this->data = array_merge($this->data, $datas);
    return $this;
  }

  /**
   * @return string
   */
  public function getIdentifier(): string {
    return $this->identifier;
  }

  /**
   * @param string $identifier
   * @return AbstractImportedItemResult
   */
  public function setIdentifier(string $identifier): AbstractImportedItemResult {
    $this->identifier = $identifier;
    return $this;
  }
}
