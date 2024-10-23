<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Entity\Abstract\UuidEntity;
use App\Repository\MemberSeasonRepository;
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
#[ApiResource(
  uriTemplate: '/members/{uuid}/seasons.{_format}',
  operations: [
    new GetCollection(),
  ],
  uriVariables: [
    'uuid' => new Link(toProperty: 'member', fromClass: Member::class),
  ],
  normalizationContext: [
    'groups' => ['member-season', 'member-season-read']
  ],
  order: ['season.name' => 'DESC'],
)]
class MemberSeason extends UuidEntity {

  #[ORM\ManyToOne(targetEntity: Member::class, inversedBy: 'memberSeasons')]
  #[Groups(['member-season-read'])]
  private ?Member $member = null;

  #[ORM\ManyToOne(targetEntity: Season::class, inversedBy: 'memberSeasons')]
  #[Groups(['member-season-read'])]
  private ?Season $season = null;

  #[ORM\ManyToOne(targetEntity: AgeCategory::class, inversedBy: 'memberSeasons')]
  #[Groups(['member-season-read', 'member-read', 'member-presence-read'])]
  private ?AgeCategory $ageCategory = null;

  #[ORM\Column(type: 'boolean', options: ["default" => 0])]
  #[Groups(['member-season-read', 'member-read', 'member-presence-read'])]
  private bool $isSecondaryClub = false;

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

  public function getIsSecondaryClub(): bool {
    return $this->isSecondaryClub;
  }

  public function setIsSecondaryClub(bool $isSecondaryClub): MemberSeason {
    $this->isSecondaryClub = $isSecondaryClub;
    return $this;
  }
}
