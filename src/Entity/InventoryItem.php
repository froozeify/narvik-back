<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Abstract\UuidEntity;
use App\Entity\Interface\TimestampEntityInterface;
use App\Entity\Trait\TimestampTrait;
use App\Filter\MultipleFilter;
use App\Repository\InventoryItemRepository;
use App\Service\UtilsService;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InventoryItemRepository::class)]
#[UniqueEntity(fields: ['name', 'category'], message: 'An item with the same name is already defined' )]
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
    'groups' => ['inventory-item', 'inventory-item-write']
  ],
  order: ['category.weight' => 'ASC', 'name' => 'ASC']
)]
#[ApiFilter(OrderFilter::class, properties: ['name' => 'ASC', 'category.name' => 'ASC', 'category.weight' => 'ASC', 'quantity' => ['default_direction' => 'ASC', 'nulls_comparison' => OrderFilter::NULLS_ALWAYS_LAST ]])]
#[ApiFilter(MultipleFilter::class, properties: ['name', 'barcode'])]
#[ApiFilter(SearchFilter::class, properties: ['category.uuid' => 'exact'])]
#[ApiFilter(BooleanFilter::class, properties: ['canBeSold'])]
#[ApiFilter(ExistsFilter::class, properties: ['sellingPrice'])]
class InventoryItem extends UuidEntity implements TimestampEntityInterface {
  use TimestampTrait;

  #[ORM\Column(length: 255)]
  #[Groups(['inventory-item', 'sale-read'])]
  #[Assert\NotBlank]
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

  #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
  #[Groups(['inventory-item'])]
  #[Assert\NotNull]
  private ?string $sellingPrice = null;

  #[ORM\Column(nullable: false)]
  #[Groups(['inventory-item'])]
  #[Assert\GreaterThanOrEqual(value: 1)]
  #[Assert\NotBlank]
  private int $sellingQuantity = 1;

  #[ORM\Column(nullable: true)]
  #[Groups(['inventory-item'])]
  private ?int $quantity = null;

  #[ORM\Column(nullable: true)]
  #[Groups(['inventory-item'])]
  private ?int $quantityAlert = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups(['inventory-item'])]
  private ?string $barcode = null;

  #[ORM\ManyToOne(inversedBy: 'items')]
  #[Groups(['inventory-item'])]
  private ?InventoryCategory $category = null;

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(string $name): static {
    $this->name = ucfirst($name);
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
    $this->purchasePrice = UtilsService::convertStringToDbDecimal($purchasePrice);
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

  public function setSellingPrice(string $sellingPrice): static {
    $this->sellingPrice = UtilsService::convertStringToDbDecimal($sellingPrice);
    return $this;
  }

  public function getSellingQuantity(): int {
    return $this->sellingQuantity;
  }

  public function setSellingQuantity(int $sellingQuantity): static {
    $this->sellingQuantity = $sellingQuantity;
    return $this;
  }

  public function getQuantityAlert(): ?int {
    return $this->quantityAlert;
  }

  public function setQuantityAlert(?int $quantityAlert): static {
    $this->quantityAlert = $quantityAlert;
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
