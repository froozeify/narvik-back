<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Abstract\UuidEntity;
use App\Entity\Interface\TimestampEntityInterface;
use App\Entity\Trait\TimestampTrait;
use App\Repository\ClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClubRepository::class)]
#[ApiResource(operations: [
  new GetCollection(security: "is_granted('ROLE_SUPER_ADMIN')"),
  new Get(),
  new Post(security: "is_granted('ROLE_SUPER_ADMIN')"),
  new Patch(security: "is_granted('ROLE_ADMIN')",),
  new Delete(security: "is_granted('ROLE_SUPER_ADMIN')",),
], normalizationContext: [
  'groups' => ['club', 'club-read'],
], denormalizationContext: [
  'groups' => ['club', 'club-write'],
], order: ['name' => 'ASC'],)]
#[ApiFilter(OrderFilter::class, properties: ['name' => 'ASC', 'createdAt' => 'DESC'])]
class Club extends UuidEntity implements TimestampEntityInterface {
  use TimestampTrait;

  #[ORM\Column(length: 255)]
  #[Groups(['club-read', 'self-read', 'super-admin-write'])]
  #[Assert\NotBlank]
  private ?string $name = null;

  #[ORM\Column(options: ['default' => false])]
  #[Groups(['club-read', 'admin-write'])]
  private bool $smtpEnabled = false;

  #[ORM\Column(options: ['default' => false])]
  #[Groups(['club-read', 'admin-write'])]
  private bool $salesEnabled = false;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups(['club-read', 'admin-write'])]
  #[ApiProperty(security: "is_granted('CLUB_ADMIN', object)")] // Property only viewable & writable by the club admin
  private ?string $badgerToken = null;

  public function __construct() {
    parent::__construct();
  }

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(string $name): static {
    $this->name = $name;
    return $this;
  }

  public function getSmtpEnabled(): ?bool {
    return $this->smtpEnabled;
  }

  public function setSmtpEnabled(bool $smtpEnabled): static {
    $this->smtpEnabled = $smtpEnabled;
    return $this;
  }

  public function getSalesEnabled(): ?bool {
    return $this->salesEnabled;
  }

  public function setSalesEnabled(bool $salesEnabled): static {
    $this->salesEnabled = $salesEnabled;
    return $this;
  }

  public function getBadgerToken(): ?string {
      return $this->badgerToken;
  }

  public function setBadgerToken(?string $badgerToken): static {
      $this->badgerToken = $badgerToken;
      return $this;
  }

}
