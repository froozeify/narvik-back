<?php

namespace App\Enum;

enum UserSecurityCodeTrigger: string {
  case resetPassword = 'RESET_PASSWORD';
  case accountValidation = 'ACCOUNT_VALIDATION';
}
