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
use App\Entity\ClubDependent\ClubSetting;
use App\Entity\Interface\TimestampEntityInterface;
use App\Entity\Trait\TimestampTrait;
use App\Enum\ClubRole;
use App\Enum\UserRole;
use App\Repository\ClubRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClubRepository::class)]
#[ApiResource(operations: [
  new GetCollection(security: "is_granted('".UserRole::super_admin->value."')"), // Collection only to super admin, other should get them through /self
  new Get(),
  new Post(security: "is_granted('".UserRole::super_admin->value."')"),
  new Patch(security: "is_granted('".UserRole::super_admin->value."')",),
  new Delete(security: "is_granted('".UserRole::super_admin->value."')",),
], normalizationContext: [
  'groups' => ['club', 'club-read', 'common-read'],
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

  // TODO: Add club logo field

  #[ORM\Column(options: ['default' => false])]
  #[Groups(['club-read', 'self-read', 'super-admin-write'])]
  #[ApiProperty(security: "is_granted('".ClubRole::supervisor->value."', object)")] // Property can be read by club admin/supervisor
  private bool $salesEnabled = false;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups(['club-read', 'club-admin-write'])]
  #[ApiProperty(security: "is_granted('".ClubRole::admin->value."', object)")] // Property only viewable & writable by the club admin
  private ?string $badgerToken = null;

  #[ORM\OneToOne(mappedBy: 'club', targetEntity: ClubSetting::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
  #[Groups(['club-read', 'self-read'])]
  #[ApiProperty(security: "is_granted('".ClubRole::supervisor->value."', object)")] // Property can be read by club admin/supervisor
  private ?ClubSetting $settings = null;

  public function __construct() {
    parent::__construct();
    $this->setSettings(new ClubSetting());
  }

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(string $name): static {
    $this->name = $name;
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

  public function getSettings(): ?ClubSetting {
    return $this->settings;
  }

  public function setSettings(ClubSetting $setting): static {
    // set the owning side of the relation if necessary
    if ($setting->getClub() !== $this) {
      $setting->setClub($this);
    }

    $this->settings = $setting;
    return $this;
  }

}
