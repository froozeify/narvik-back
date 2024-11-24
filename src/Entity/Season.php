<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\ClubDependent\MemberSeason;
use App\Repository\SeasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SeasonRepository::class)]
#[ApiResource(
  normalizationContext: [
    'groups' => ['season', 'season-read']
  ],
  order: ['name' => 'DESC'],
)]
class Season {
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
  #[ORM\Column]
  #[Groups(['season-read', 'member-season-read', 'member-presence-read'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Groups(['super-admin-write', 'season-read', 'member-season-read', 'member-presence-read'])]
  private ?string $name = null;

  #[ORM\OneToMany(mappedBy: 'season', targetEntity: MemberSeason::class)]
  #[Groups(['season-read'])]
  private Collection $memberSeasons;

  public function __construct() {
    $this->memberSeasons = new ArrayCollection();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(string $name): static {
    $this->name = $name;
    return $this;
  }

  /**
   * @return Collection<int, MemberSeason>
   */
  public function getMemberSeasons(): Collection {
    return $this->memberSeasons;
  }

  public function addMemberSeason(MemberSeason $memberSeason): static {
    if (!$this->memberSeasons->contains($memberSeason)) {
      $this->memberSeasons->add($memberSeason);
      $memberSeason->setSeason($this);
    }
    return $this;
  }

  public function removeMemberSeason(MemberSeason $memberSeason): static {
    if ($this->memberSeasons->removeElement($memberSeason)) {
      // set the owning side to null (unless already changed)
      if ($memberSeason->getSeason() === $this) {
        $memberSeason->setSeason(null);
      }
    }
    return $this;
  }
}
