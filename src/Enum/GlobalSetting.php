<?php

namespace App\Enum;

// TODO: All those should be migrated into ClubConfig
enum GlobalSetting {
//  case BADGER_TOKEN;
//  case CONTROL_SHOOTING_ACTIVITY_ID;
//  case IGNORED_ACTIVITIES_OPENING_STATS;
//  case LAST_ITAC_IMPORT;
//  case LAST_SECONDARY_CLUB_ITAC_IMPORT;

  case LOGO;

  // Email configuration
  case SMTP_ON;
  case SMTP_HOST;
  case SMTP_PORT;
  case SMTP_USERNAME;
  case SMTP_PASSWORD;
  case SMTP_SENDER;
  case SMTP_SENDER_NAME;
}
