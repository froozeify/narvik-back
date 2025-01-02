<?php

namespace App\Entity\ClubDependent\Plugin\Sale;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model;
use App\Controller\ClubDependent\Plugin\Sale\InventoryCategoryMove;
use App\Entity\Abstract\UuidEntity;
use App\Entity\Club;
use App\Entity\Interface\ClubLinkedEntityInterface;
use App\Entity\Interface\SortableEntityInterface;
use App\Entity\InventoryItem;
use App\Repository\InventoryCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: InventoryCategoryRepository::class)]
#[UniqueEntity(fields: ['club', 'weight'], ignoreNull: true)]
#[UniqueEntity(fields: ['club', 'name'], ignoreNull: true)]
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
    )
  ],
  normalizationContext: [
    'groups' => ['inventory-category', 'inventory-category-read']
  ],
  denormalizationContext: [
    'groups' => ['inventory-category', 'inventory-category-write']
  ],
  order: ['weight' => 'asc'],
)]
#[ApiResource(
  uriTemplate: '/clubs/{clubUuid}/inventory-categories.{_format}',
  operations: [
    new GetCollection(),

    new Put(
      uriTemplate: '/clubs/{clubUuid}/inventory-categories/{uuid}/move',
      controller: InventoryCategoryMove::class,
      openapi: new Model\Operation(
        description: 'Move `up` or `down` an inventory category',
        requestBody: new Model\RequestBody(
          content: new \ArrayObject([
            'application/json' => [
              'schema' => [
                'type' => 'object',
                'properties' => [
                  'direction' => ['type' => 'string'],
                ]
              ]
            ]
          ])
        )
      ),

      security: "is_granted('ROLE_ADMIN')",
      read: false,
      write: false,
    )
  ],
  uriVariables: [
    'clubUuid' => new Link(toProperty: 'club', fromClass: Club::class),
  ],
  normalizationContext: [
    'groups' => ['inventory-category', 'inventory-category-read']
  ],
  order: ['weight' => 'asc'],
)]
#[ApiFilter(OrderFilter::class, properties: ['weight' => 'ASC', 'name' => 'ASC'])]
class InventoryCategory extends UuidEntity implements ClubLinkedEntityInterface, SortableEntityInterface {
  public static function getClubSqlPath(): string {
    return "club";
  }

  #[ORM\ManyToOne(cascade: ['remove'])]
  #[ORM\JoinColumn(nullable: false)]
  #[Groups(['inventory-category-write'])]
  private ?Club $club = null;

  #[ORM\Column(length: 255)]
  #[Groups(['inventory-category', 'inventory-item-read'])]
  #[Assert\NotBlank]
  private ?string $name = null;

  #[ORM\Column(nullable: true)]
  #[Groups(['inventory-category-read'])]
  private ?int $weight = null;

  #[ORM\OneToMany(mappedBy: 'category', targetEntity: InventoryItem::class, orphanRemoval: true)]
  #[Groups(['inventory-category-read'])]
  private Collection $items;

  public function __construct() {
    parent::__construct();
    $this->items = new ArrayCollection();
  }

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(string $name): static {
    $this->name = ucfirst($name);
    return $this;
  }

  public function getWeight(): ?int {
    return $this->weight;
  }

  public function setWeight(int $weight): static {
    $this->weight = $weight;
    return $this;
  }

  /**
   * @return Collection<int, InventoryItem>
   */
  public function getItems(): Collection {
      return $this->items;
  }

  public function addItem(InventoryItem $item): static {
      if (!$this->items->contains($item)) {
          $this->items->add($item);
          $item->setCategory($this);
      }
      return $this;
  }

  public function removeItem(InventoryItem $item): static {
      if ($this->items->removeElement($item)) {
          // set the owning side to null (unless already changed)
          if ($item->getCategory() === $this) {
              $item->setCategory(null);
          }
      }
      return $this;
  }

  public function getClub(): ?Club {
    return $this->club;
  }

  public function setClub(?Club $club): static {
    $this->club = $club;
    return $this;
  }
}
