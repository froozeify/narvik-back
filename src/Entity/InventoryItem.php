<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Filter\MultipleFilter;
use App\Repository\InventoryItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InventoryItemRepository::class)]
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
    'groups' => ['inventory-item', 'inventory-item-read']
  ],
  denormalizationContext: [
    'groups' => ['admin-write']
  ]
)]
#[ApiFilter(OrderFilter::class, properties: ['name' => 'ASC', 'category.name' => 'ASC'])]
#[ApiFilter(MultipleFilter::class, properties: ['name', 'barcode'])]
#[ApiFilter(SearchFilter::class, properties: ['category.id' => 'exact'])]
class InventoryItem {
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['admin-write', 'inventory-item-read'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Groups(['inventory-item'])]
  private ?string $name = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups(['inventory-item'])]
  private ?string $description = null;

  #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
  #[Groups(['inventory-item'])]
  private ?string $purchasePrice = null;

  #[ORM\Column]
  #[Groups(['inventory-item'])]
  private ?bool $canBeSold = null;

  /**
   * null = price to be set on sale (useful fpr the Other/Donation item)
   */
  #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
  #[Groups(['inventory-item'])]
  private ?string $sellingPrice = null;

  #[ORM\Column(nullable: false)]
  #[Groups(['inventory-item'])]
  #[Assert\GreaterThanOrEqual(value: 1)]
  #[Assert\NotBlank]
  private int $sellingQuantity = 1;

  #[ORM\Column(nullable: true)]
  #[Groups(['inventory-item'])]
  private ?int $quantity = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups(['inventory-item'])]
  private ?string $barcode = null;

  #[ORM\ManyToOne(inversedBy: 'items')]
  #[Groups(['inventory-item-read'])]
  private ?InventoryCategory $category = null;

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

  public function getDescription(): ?string {
    return $this->description;
  }

  public function setDescription(?string $description): static {
    $this->description = $description;
    return $this;
  }

  public function getPurchasePrice(): ?string {
    return $this->purchasePrice;
  }

  public function setPurchasePrice(?string $purchasePrice): static {
    $this->purchasePrice = $purchasePrice;
    return $this;
  }

  public function getCanBeSold(): ?bool {
    return $this->canBeSold;
  }

  public function setCanBeSold(bool $canBeSold): static {
    $this->canBeSold = $canBeSold;
    return $this;
  }

  public function getSellingPrice(): ?string {
    return $this->sellingPrice;
  }

  public function setSellingPrice(?string $sellingPrice): static {
    $this->sellingPrice = $sellingPrice;
    return $this;
  }

  public function getSellingQuantity(): int {
    return $this->sellingQuantity;
  }

  public function setSellingQuantity(int $sellingQuantity): static {
    $this->sellingQuantity = $sellingQuantity;
    return $this;
  }

  public function getQuantity(): ?int {
    return $this->quantity;
  }

  public function setQuantity(?int $quantity): static {
    $this->quantity = $quantity;
    return $this;
  }

  public function getBarcode(): ?string {
    return $this->barcode;
  }

  public function setBarcode(?string $barcode): static {
    $this->barcode = $barcode;
    return $this;
  }

  public function getCategory(): ?InventoryCategory {
    return $this->category;
  }

  public function setCategory(?InventoryCategory $category): static {
    $this->category = $category;
    return $this;
  }
}
