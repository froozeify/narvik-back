<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AgeCategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AgeCategoryRepository::class)]
#[ApiResource(
  normalizationContext: [
    'groups' => ['age-category', 'age-category-read']
  ]
)]
class AgeCategory {
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
  #[ORM\Column]
  #[Groups(['age-category-read', 'member-read', 'member-presence-read'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Groups(['super-admin-write', 'age-category-read', 'member-read', 'member-presence-read'])]
  #[Assert\NotBlank]
  private ?string $code = null;

  #[ORM\Column(length: 255)]
  #[Groups(['super-admin-write', 'age-category-read', 'member-read', 'member-presence-read'])]
  #[Assert\NotBlank]
  private ?string $name = null;

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
}
