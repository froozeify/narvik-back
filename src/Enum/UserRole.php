<?php

namespace App\Enum;

enum UserRole: string {
  case user = 'ROLE_USER';

  case badger = 'ROLE_BADGER';

  case member = 'ROLE_MEMBER';
  case admin = 'ROLE_ADMIN';
  case super_admin = 'ROLE_SUPER_ADMIN';
}
