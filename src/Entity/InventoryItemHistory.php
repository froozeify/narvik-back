<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Entity\Interface\TimestampEntityInterface;
use App\Entity\Trait\TimestampTrait;
use App\Repository\InventoryItemHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: InventoryItemHistoryRepository::class)]
#[ApiResource(
  uriTemplate: '/inventory-items/{itemId}/histories.{_format}',
  operations: [
    new GetCollection(),
  ],
  uriVariables: [
    'itemId' => new Link(toProperty: 'item', fromClass: InventoryItem::class),
  ],
  normalizationContext: [
    'groups' => ['inventory-item-history', 'inventory-item-history-read']
  ],
  order: ['createdAt' => 'DESC'],
)]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC'])]
class InventoryItemHistory implements TimestampEntityInterface {
  use TimestampTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['inventory-item-history-read'])]
  private ?int $id = null;

  #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
  #[Groups(['inventory-item-history-read'])]
  private ?string $sellingPrice = null;

  #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
  #[Groups(['inventory-item-history-read'])]
  private ?string $purchasePrice = null;

  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
  private ?InventoryItem $item = null;

  public function getId(): ?int {
    return $this->id;
  }

  public function getSellingPrice(): ?string {
    return $this->sellingPrice;
  }

  public function setSellingPrice(?string $sellingPrice): static {
    $this->sellingPrice = $sellingPrice;
    return $this;
  }

  public function getPurchasePrice(): ?string {
    return $this->purchasePrice;
  }

  public function setPurchasePrice(?string $purchasePrice): static {
    $this->purchasePrice = $purchasePrice;
    return $this;
  }

  public function getItem(): ?InventoryItem {
    return $this->item;
  }

  public function setItem(?InventoryItem $item): static {
    $this->item = $item;
    return $this;
  }
}
