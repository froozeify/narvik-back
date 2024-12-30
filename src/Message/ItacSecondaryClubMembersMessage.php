<?php

namespace App\Message;

use App\Message\Abstract\ClubLinkedMessage;

class ItacSecondaryClubMembersMessage extends ClubLinkedMessage {

  public function getClubSettingRemainingField(): string {
    return "itacSecondaryImportRemaining";
  }

  public function __construct(
    string $clubUuid,
    private readonly array $records,
  ) {
    parent::__construct($clubUuid);
  }

  public function getRecords(): array {
    return $this->records;
  }
}
