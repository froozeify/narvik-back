<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
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
use App\Entity\ClubDependent\Member;
use App\Entity\Interface\TimestampEntityInterface;
use App\Entity\Trait\TimestampTrait;
use App\Repository\SaleRepository;
use App\Service\UtilsService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: SaleRepository::class)]
#[ApiResource(
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch(
      security: "is_granted('ROLE_ADMIN') || is_granted('SALE_UPDATE', object)",
    ),
    new Delete(
      security: "is_granted('ROLE_ADMIN') || is_granted('SALE_DELETE', object)",
    ),
  ],
  normalizationContext: [
    'groups' => ['sale', 'sale-read']
  ],
  denormalizationContext: [
    'groups' => ['sale', 'sale-write', 'timestamp-write-create']
  ],
  order: ['createdAt' => 'DESC'],
  paginationClientEnabled: true,
)]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC'])]
#[ApiFilter(SearchFilter::class, properties: ['seller.uuid' => 'exact'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt' => DateFilter::EXCLUDE_NULL])]
class Sale extends UuidEntity implements TimestampEntityInterface {
  use TimestampTrait;

  #[ORM\ManyToOne(inversedBy: 'sales')]
  #[Groups(['sale'])]
  private ?Member $seller = null;

  #[ORM\ManyToOne]
  #[Groups(['sale'])]
  #[Assert\NotNull]
  private ?SalePaymentMode $paymentMode = null;

  #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
  #[Groups(['sale'])]
  #[Assert\NotBlank(allowNull: true)] // null: Price calculated automatically on persist
  private ?string $price = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups(['sale'])]
  #[Assert\NotBlank(allowNull: true)]
  private ?string $comment = null;

  /**
   * @var Collection<int, SalePurchasedItem>
   */
  #[ORM\OneToMany(mappedBy: 'sale', targetEntity: SalePurchasedItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
  #[Groups(['sale'])]
  #[Assert\Valid]
  #[Assert\Count(min: 1)]
  private Collection $salePurchasedItems;

  public function __construct() {
    parent::__construct();
    $this->salePurchasedItems = new ArrayCollection();
  }

  public function getSeller(): ?Member {
    return $this->seller;
  }

  public function setSeller(?Member $seller): static {
    $this->seller = $seller;
    return $this;
  }

  public function getPaymentMode(): ?SalePaymentMode {
    return $this->paymentMode;
  }

  public function setPaymentMode(?SalePaymentMode $paymentMode): static {
    $this->paymentMode = $paymentMode;
    return $this;
  }

  public function getPrice(): ?string {
    return $this->price;
  }

  public function setPrice(string $price): static {
    $this->price = UtilsService::convertStringToDbDecimal($price);
    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }

  public function setComment(?string $comment): static {
    $this->comment = $comment;
    return $this;
  }

  /**
   * @return Collection<int, SalePurchasedItem>
   */
  public function getSalePurchasedItems(): Collection {
    return $this->salePurchasedItems;
  }

  public function addSalePurchasedItem(SalePurchasedItem $salePurchasedItem): static {
    if (!$this->salePurchasedItems->contains($salePurchasedItem)) {
      $this->salePurchasedItems->add($salePurchasedItem);
      $salePurchasedItem->setSale($this);
    }
    return $this;
  }

  public function removeSalePurchasedItem(SalePurchasedItem $salePurchasedItem): static {
    if ($this->salePurchasedItems->removeElement($salePurchasedItem)) {
      // set the owning side to null (unless already changed)
      if ($salePurchasedItem->getSale() === $this) {
        $salePurchasedItem->setSale(null);
      }
    }
    return $this;
  }
}
