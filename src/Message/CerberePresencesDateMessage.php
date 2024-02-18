<?php

namespace App\Message;

class CerberePresencesDateMessage {
  public function __construct(
    private \DateTimeImmutable $date,
    private array $datas
  ) {

  }

  public function getDate(): \DateTimeImmutable {
    return $this->date;
  }

  public function getDatas(): array {
    return $this->datas;
  }
}
