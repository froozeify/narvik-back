<?php

namespace App\Enum;

enum ClubRole: string {
  case admin = 'CLUB_ADMIN';
  case supervisor = 'CLUB_SUPERVISOR';
  case member = 'CLUB_MEMBER';

  case badger = 'CLUB_BADGER';

  public function hasRole(ClubRole $role): bool {
    return match ($role->value) {
      self::admin->value => $this->isAdmin(), // Admin have all role
      self::supervisor->value => $this->hasSupervisorRole(),
      // Member and badger only have their own level role
      default => $role === $this,
    };
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
