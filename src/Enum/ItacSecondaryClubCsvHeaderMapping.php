<?php

namespace App\Enum;

enum ItacSecondaryClubCsvHeaderMapping: string {
  case GENDER = 'Sexe'; // M
  case LICENCE = 'N° licence';
  case LASTNAME = 'Nom';
  case FIRSTNAME = 'Prénom';

  case POSTAL_1 = 'Adresse 1';
  case POSTAL_2 = 'Adresse 2';
  case POSTAL_3 = 'Adresse 3';
  case ZIP_CODE = 'Code postal';
  case CITY = 'Ville';

  case EMAIL = 'Mail';
  case PHONE = 'Téléphone';
  case MOBILE_PHONE = 'Tél. mobile';


  case SEASON = 'Saison';
  case AGE_CATEGORY = 'Catégorie d\'âge'; // Senior 3
}
