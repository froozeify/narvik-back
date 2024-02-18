<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\State\MetricProvider;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
  provider: MetricProvider::class,
  normalizationContext: [
    'groups' => ['metric']
  ]
)]
#[Get]
#[GetCollection]
class Metric {

  #[ApiProperty(identifier: true)]
  #[Groups(['metric'])]
  private string $name;

  #[Groups(['metric'])]
  private float $value = 0;

  #[Groups(['metric'])]
  #[ApiProperty(jsonldContext:["@type" => "#Metric[]"])]
  private array $childMetrics = [];

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

  public function getChildMetrics(): ?array {
    return $this->childMetrics;
  }

  public function setChildMetrics(?array $childMetrics): Metric {
    $this->childMetrics = $childMetrics;
    return $this;
  }
}
