<?php

namespace App\Entity\Trait;

use ApiPlatform\Metadata\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidV7Generator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

trait UuidTrait {
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
  #[ORM\Column]
  #[ApiProperty(identifier: false)]
  protected ?int $id = null;

  #[ApiProperty(identifier: true)]
  #[ORM\Column(type: 'uuid', unique: true)]
  #[ORM\CustomIdGenerator(class: UuidV7Generator::class)]
  #[Groups(['common-read'])]
  protected ?UuidInterface $uuid = null;

  public function getId(): ?int {
    return $this->id;
  }

  public function getUuid(): ?UuidInterface {
    return $this->uuid;
  }

  public function setUuid(UuidInterface $uuid): static {
    $this->uuid = $uuid;
    return $this;
  }
}