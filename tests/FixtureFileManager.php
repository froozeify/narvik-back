<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FixtureFileManager {
  const string PROFILE_PICTURES = 'profile-pictures.zip';
  const string ITAC_MEMBERS = 'itac-members.csv';
  const string ITAC_SECONDARY_MEMBERS = 'itac-secondary-members.csv';
  const string PRESENCES_NARVIK = 'presences-narvik.csv';
  const string PRESENCES_CERBERE = 'presences-cerbere.xls';

  public static function getUploadedFile(string $filename): UploadedFile {
    return new UploadedFile(__DIR__ . '/fixtures/' . $filename, $filename);
  }
}
