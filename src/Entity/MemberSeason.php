<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MemberSeasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MemberSeasonRepository::class)]
#[ApiResource(
  normalizationContext: [
    'groups' => ['member-season', 'member-season-read']
  ],
  denormalizationContext: [
    'groups' => ['member-season', 'member-season-write']
  ]
)]
class MemberSeason {
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
  #[ORM\Column]
  #[Groups(['member-season-read'])]
  private ?int $id = null;

  #[ORM\ManyToOne(targetEntity: Member::class, inversedBy: 'memberSeasons')]
  #[Groups(['member-season-read'])]
  private ?Member $member = null;

  #[ORM\ManyToOne(targetEntity: Season::class, inversedBy: 'memberSeasons')]
  #[Groups(['member-season-read'])]
  private ?Season $season = null;

  #[ORM\ManyToOne(targetEntity: AgeCategory::class, inversedBy: 'memberSeasons')]
  #[Groups(['member-season-read'])]
  private ?AgeCategory $ageCategory = null;

  public function __construct() { }

  public function getId(): ?int {
    return $this->id;
  }

  public function getSeason(): ?Season {
    return $this->season;
  }

  public function setSeason(?Season $season): static {
    $this->season = $season;
    return $this;
  }

  public function getAgeCategory(): ?AgeCategory {
    return $this->ageCategory;
  }

  public function setAgeCategory(?AgeCategory $ageCategory): static {
    $this->ageCategory = $ageCategory;
    return $this;
  }

  public function getMember(): ?Member {
    return $this->member;
  }

  public function setMember(?Member $member): static {
    $this->member = $member;
    return $this;
  }
}