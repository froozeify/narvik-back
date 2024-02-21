<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Controller\GlobalSettingGetPublic;
use App\Controller\GlobalSettingImportLogo;
use App\Repository\GlobalSettingRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: GlobalSettingRepository::class)]
#[ApiResource(
  operations: [
    new GetCollection(),
    new Get(),
    new Patch(),

    new Get(
      uriTemplate: '/public/global-settings/{name}',
      controller: GlobalSettingGetPublic::class,
    ),

    new Post(
      uriTemplate: '/global-settings/-/logo',
      controller: GlobalSettingImportLogo::class,
      openapi: new Model\Operation(
        requestBody: new Model\RequestBody(
          content: new \ArrayObject([
            'multipart/form-data' => [
              'schema' => [
                'type' => 'object',
                'properties' => [
                  'file' => [
                    'type' => 'string',
                    'format' => 'binary'
                  ]
                ]
              ]
            ]
          ])
        )
      ),
      security: "is_granted('ROLE_ADMIN')",
      deserialize: false,
    )
  ]
)]
class GlobalSetting {
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
  #[ORM\Column]
  #[ApiProperty(identifier: false)]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[ApiProperty(identifier: true)]
  private string $name;

  #[ORM\Column(length: 255, nullable: true)]
  private string|null $value = null;

  public function getId(): ?int {
    return $this->id;
  }

  public function getName(): string {
    return $this->name;
  }

  public function setName(string $name): static {
    $this->name = $name;
    return $this;
  }

  public function getValue(): ?string {
    return $this->value;
  }

  public function setValue(?string $value): static {
    $this->value = $value;
    return $this;
  }
}
