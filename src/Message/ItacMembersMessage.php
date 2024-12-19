<?php

namespace App\Message;

use App\Entity\Club;

class ItacMembersMessage {

  //TODO: Add a message batch id, so we can log in db after the success/warnings with some stats
  public function __construct(
    private readonly Club $club,
    private readonly array $records,
  ) {
  }

  public function getClub(): Club {
    return $this->club;
  }

  public function getRecords(): array {
    return $this->records;
  }
}
