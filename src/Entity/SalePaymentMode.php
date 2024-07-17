<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model;
use App\Controller\SalePaymentModeMove;
use App\Entity\Interface\SortableEntityInterface;
use App\Repository\SalePaymentModeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SalePaymentModeRepository::class)]
#[UniqueEntity(fields: ['weight'], ignoreNull: true)]
#[UniqueEntity(fields: ['name'], ignoreNull: true)]
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
      uriTemplate: '/sale-payment-modes/{id}/move',
      controller: SalePaymentModeMove::class,
      openapi: new Model\Operation(
        description: 'Move `up` or `down` a payment mode',
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
    'groups' => ['sale-payment-mode', 'sale-payment-mode-read']
  ],
  denormalizationContext: [
    'groups' => ['sale-payment-mode', 'sale-payment-mode-write']
  ],
  order: ['weight' => 'asc'],
)]
#[ApiFilter(OrderFilter::class, properties: ['weight' => 'ASC'])]
#[ApiFilter(BooleanFilter::class, properties: ['available'])]
class SalePaymentMode implements SortableEntityInterface {
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['sale-payment-mode-read'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Groups(['sale-payment-mode'])]
  #[Assert\NotBlank]
  private ?string $name = null;

  #[ORM\Column(length: 255)]
  #[Assert\NotBlank]
  #[Groups(['sale-payment-mode'])]
  private ?string $icon = null;

  #[ORM\Column]
  #[Groups(['sale-payment-mode'])]
  #[Assert\NotNull]
  private ?bool $available = true;

  #[ORM\Column(nullable: true)]
  #[Groups(['sale-payment-mode-read'])]
  private ?int $weight = null;

  public function getId(): ?int {
    return $this->id;
  }

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(string $name): static {
    $this->name = ucfirst($name);
    return $this;
  }

  public function getIcon(): ?string {
    return $this->icon;
  }

  public function setIcon(string $icon): static {
    $this->icon = $icon;
    return $this;
  }

  public function isAvailable(): ?bool {
    return $this->available;
  }

  public function setAvailable(bool $available): static {
    $this->available = $available;
    return $this;
  }

  public function getWeight(): ?int {
    return $this->weight;
  }

  public function setWeight(?int $weight): static {
    $this->weight = $weight;
    return $this;
  }
}
