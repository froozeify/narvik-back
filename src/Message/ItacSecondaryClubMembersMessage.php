<?php

namespace App\Message;

class ItacSecondaryClubMembersMessage {

  //TODO: Add a message batch id, so we can log in db after the success/warnings with some stats
  public function __construct(
    private readonly array $records,
  ) {
  }

  public function getRecords(): array {
    return $this->records;
  }
}
