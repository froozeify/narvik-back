<?php

namespace App\Repository\Interface;

use App\Entity\Club;

interface ClubLinkedInterface {
    public function findAllByClub(Club $club): array;
}
