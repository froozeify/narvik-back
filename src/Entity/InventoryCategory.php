<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\ActivityMergeTo;
use App\Repository\InventoryCategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: InventoryCategoryRepository::class)]
#[UniqueEntity(fields: ['weight'], ignoreNull: true)]
#[ApiResource(
  operations: [
    new GetCollection(),
    new Get(),
    new Post(
      security: "is_granted('ROLE_ADMIN')"
    ),
    new Patch(
      security: "is_granted('ROLE_ADMIN')"
    ),
    new Delete(
      security: "is_granted('ROLE_ADMIN')"
    ),
  ],
  normalizationContext: [
    'groups' => ['inventory-category', 'inventory-category-read']
  ],
  denormalizationContext: [
    'groups' => ['admin-write']
  ],
  paginationEnabled: false
)]
class InventoryCategory {
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['admin-write', 'inventory-category-read'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Groups(['inventory-category'])]
  private ?string $name = null;

  #[ORM\Column]
  #[Groups(['admin-write', 'inventory-category-read'])]
  private ?int $weight = null;

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

  public function getWeight(): ?int {
    return $this->weight;
  }

  public function setWeight(int $weight): static {
    $this->weight = $weight;
    return $this;
  }
}
