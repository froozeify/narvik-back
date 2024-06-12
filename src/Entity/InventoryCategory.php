<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model;
use App\Controller\InventoryCategoryMove;
use App\Entity\Interface\SortableEntityInterface;
use App\Repository\InventoryCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    new Put(
      uriTemplate: '/inventory-categories/{id}/move',
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
  normalizationContext: [
    'groups' => ['inventory-category', 'inventory-category-read']
  ],
  denormalizationContext: [
    'groups' => ['inventory-category', 'inventory-category-write']
  ],
  // paginationEnabled: false
)]
class InventoryCategory implements SortableEntityInterface {
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['inventory-category-read', 'inventory-item-read'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Groups(['inventory-category', 'inventory-item-read'])]
  private ?string $name = null;

  #[ORM\Column]
  #[Groups(['inventory-category-read'])]
  private ?int $weight = null;

  #[ORM\OneToMany(mappedBy: 'category', targetEntity: InventoryItem::class)]
  #[Groups(['inventory-category-read'])]
  private Collection $items;

  public function __construct() {
      $this->items = new ArrayCollection();
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
}
