<?php

namespace App\Message;

class MemberPresencesCsvMessage {

  public function __construct(
    private readonly string $clubUuid,
    private readonly array $records,
  ) {
  }

  public function getRecords(): array {
    return $this->records;
  }

  public function getClubUuid(): string {
    return $this->clubUuid;
  }
}
