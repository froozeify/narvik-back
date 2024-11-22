<?php

namespace App\Entity\Trait;

use App\Entity\Club;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

trait SelfClubLinkedEntityTrait {
  public static function getClubSqlPath(): string {
    return 'club';
  }

  #[ORM\ManyToOne(targetEntity: Club::class)]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
  #[Groups(['common-read', 'club-admin-write'])]
  private ?Club $club = null;

  public function getClub(): ?Club {
    return $this->club;
  }

  public function setClub(?Club $club): static {
    $this->club = $club;
    return $this;
  }
}
