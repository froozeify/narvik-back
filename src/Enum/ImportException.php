<?php

namespace App\Enum;

enum ImportException: string {
  case FILE_NOT_READABLE = "File not readable";
  case COL_MISSING_REQUIRED = "Missing required value";
}
