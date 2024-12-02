<?php

namespace App\Enum;

enum ClubRole: string {
  case admin = 'CLUB_ADMIN';
  case supervisor = 'CLUB_SUPERVISOR';
  case member = 'CLUB_MEMBER';

  case badger = 'CLUB_BADGER';

  public function hasRole(ClubRole $role): bool {
    // Admin have all role
    if ($this->isAdmin()) {
      return true;
    }

    // Supervisor have all role excepted admin
    if ($this->hasSupervisorRole()) {
      return !$role->isAdmin();
    }

    // Other role have no heritage, so must be the same
    return $this === $role;
  }

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
