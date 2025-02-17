<?php

namespace App\Enum;

enum UserRole: string {
  case super_admin = 'ROLE_SUPER_ADMIN';
  case user = 'ROLE_USER';

  // Badger have very specific and limited access
  case badger = 'ROLE_BADGER';
}
