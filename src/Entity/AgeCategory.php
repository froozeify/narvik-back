<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AgeCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AgeCategoryRepository::class)]
#[ApiResource(
  normalizationContext: [
    'groups' => ['age-category', 'age-category-read']
  ],
  denormalizationContext: [
    'groups' => []
  ]
)]
class AgeCategory {
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
  #[ORM\Column]
  #[Groups(['age-category-read', 'member-read', 'member-presence-read'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Groups(['age-category-read', 'member-read', 'member-presence-read'])]
  private ?string $code = null;

  #[ORM\Column(length: 255)]
  #[Groups(['age-category-read', 'member-read', 'member-presence-read'])]
  private ?string $name = null;

  #[ORM\OneToMany(mappedBy: 'ageCategory', targetEntity: MemberSeason::class)]
  #[Groups(['age-category-read'])]
  private Collection $memberSeasons;

  public function __construct() {
    $this->memberSeasons = new ArrayCollection();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getCode(): ?string {
    return $this->code;
  }

  public function setCode(string $code): static {
    $this->code = $code;
    return $this;
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
      $memberSeason->setAgeCategory($this);
    }
    return $this;
  }

  public function removeMemberSeason(MemberSeason $memberSeason): static {
    if ($this->memberSeasons->removeElement($memberSeason)) {
      // set the owning side to null (unless already changed)
      if ($memberSeason->getAgeCategory() === $this) {
        $memberSeason->setAgeCategory(null);
      }
    }
    return $this;
  }
}
