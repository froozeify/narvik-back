<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\ImageProvider;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
  provider: ImageProvider::class,
  normalizationContext: [
    'groups' => ['image']
  ]
)]
#[Get]
class Image {

  #[ApiProperty(identifier: true)]
  #[Groups(['image'])]
  private string $id; // base64 encode of public path

  #[Groups(['image'])]
  private string $name;

  #[Groups(['image'])]
  private string $base64;

  #[Groups(['image'])]
  private string $mimeType;

  public function getId(): string {
    return $this->id;
  }

  public function setId(string $id): Image {
    $this->id = $id;
    return $this;
  }

  public function getName(): string {
    return $this->name;
  }

  public function setName(string $name): Image {
    $this->name = $name;
    return $this;
  }

  public function getBase64(): string {
    return $this->base64;
  }

  public function setBase64(string $base64): Image {
    $this->base64 = $base64;
    return $this;
  }

  public function getMimeType(): string {
    return $this->mimeType;
  }

  public function setMimeType(string $mimeType): Image {
    $this->mimeType = $mimeType;
    return $this;
  }

}
