<?php

namespace App\Message;

class ItacMembersMessage {

  //TODO: Add a message batch id, so we can log in db after the success/warnings with some stats
  public function __construct(
    private readonly string $clubUuid,
    private readonly array $records,
  ) {
  }

  public function getClubUuid(): string {
    return $this->clubUuid;
  }

  public function getRecords(): array {
    return $this->records;
  }
}
