<?php

namespace App\Enum;

enum MemberRole: string {
  case user = 'ROLE_USER';

  case badger = 'ROLE_BADGER';

  case member = 'ROLE_MEMBER';
  case supervisor = 'ROLE_SUPERVISOR';
  case admin = 'ROLE_ADMIN';
}
