<?php

namespace App\Enum;

enum ClubRole: string {

  case badger = 'ROLE_BADGER';

  case member = 'ROLE_MEMBER';
  case supervisor = 'ROLE_SUPERVISOR';
  case admin = 'ROLE_ADMIN';

  public function isAdmin(): bool {
    return $this->value === self::admin->value;
  }

  public function isSupervisor(): bool {
    return $this->value === self::supervisor->value;
  }

  public function hasSupervisorRole(): bool {
    return match ($this->value) {
      self::supervisor->value, self::admin->value => true,
      default => false,
    };
  }
}
