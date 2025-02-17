<?php

namespace App\Message\Abstract;

abstract class ClubLinkedMessage {
  public abstract function getClubSettingRemainingField(): string;

  public function __construct(
    private readonly string $clubUuid,
  ) {
  }

  public function getClubUuid(): string {
    return $this->clubUuid;
  }

}
