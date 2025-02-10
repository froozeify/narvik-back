<?php

namespace App\Entity\ClubDependent;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Entity\Club;
use App\Entity\Interface\ClubLinkedEntityInterface;
use App\Entity\Trait\SelfClubLinkedEntityTrait;
use App\Enum\ClubRole;
use App\Enum\UserRole;
use App\State\MetricProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
  operations: [
    new Get(security: "is_granted('".UserRole::super_admin->value."')"),
    new GetCollection(security: "is_granted('".UserRole::super_admin->value."')"),

    new Get(
      uriTemplate: '/clubs/{clubUuid}/metrics/{name}',
      uriVariables: [
        'clubUuid' => new Link(toProperty: 'club', fromClass: Club::class),
        'name' => new Link(fromClass: self::class),
      ],
      security: "is_granted('".ClubRole::supervisor->value."', request)",
      name: 'club_metric',
    ),
    new GetCollection(
      uriTemplate: '/clubs/{clubUuid}/metrics',
      uriVariables: [
        'clubUuid' => new Link(toProperty: 'club', fromClass: Club::class),
      ],
      security: "is_granted('".ClubRole::supervisor->value."', request)",
      name: 'club_metrics',
    ),
  ],
  normalizationContext: [
    'groups' => ['metric']
  ],
  provider: MetricProvider::class,
)]
#[Get]
#[GetCollection]
class Metric {
  private ?Club $club = null;

  #[ApiProperty(identifier: true)]
  #[Groups(['metric'])]
  private string $name;

  #[Groups(['metric'])]
  private float $value = 0;

  /**
   * @var Collection<int, Metric>
   */
  #[Groups(['metric'])]
  private Collection $childMetrics;

  public function __construct() {
    $this->childMetrics = new ArrayCollection();
  }


  public function getClub(): ?Club {
    return $this->club;
  }

  public function setClub(?Club $club): static {
    $this->club = $club;
    return $this;
  }

  public function getName(): string {
    return $this->name;
  }

  public function setName(string $name): Metric {
    $this->name = str_replace('.', '-', $name);
    return $this;
  }

  public function getValue(): float {
    return $this->value;
  }

  public function setValue(float $value): Metric {
    $this->value = $value;
    return $this;
  }

  public function getChildMetrics(): ?Collection {
    return $this->childMetrics;
  }

  public function setChildMetrics(?array $childMetrics): Metric {
    $this->childMetrics = new ArrayCollection($childMetrics);
    return $this;
  }
}
