<?php

namespace App\Entity\Interface;

use App\Entity\Club;

interface ClubLinkedEntityInterface {
  public static function getClubSqlPath(): string;
  public function getClub(): ?Club;
  public function setClub(?Club $club): static;
}
