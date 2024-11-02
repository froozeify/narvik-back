<?php

namespace App\Enum;

enum UserRole: string {
  case super_admin = 'ROLE_SUPER_ADMIN';
  case user = 'ROLE_USER';

  // This role is applied dynamically on the fake User created when a badger is login
  case badger = 'ROLE_BADGER';
}
