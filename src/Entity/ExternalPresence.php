<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Controller\ExternalPresenceToday;
use App\Repository\ExternalPresenceRepository;
use App\Validator\Constraints\ActivityMustBeEnabled;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ExternalPresenceRepository::class)]
#[UniqueEntity(fields: ['licence', 'date'], message: 'Member already registered for today')]
#[ApiResource(
  operations: [
    new GetCollection(),
    new Post(),
    new Patch(),

    new GetCollection(
      uriTemplate: '/external-presences/-/today',
      controller: ExternalPresenceToday::class,
      openapi: new Model\Operation(
        summary: 'Get all external presence for today',
      ),
      read: false,
      write: false
    )
  ],
  normalizationContext: [
    'groups' => ['external-presence', 'external-presence-read']
  ],
  denormalizationContext: [
    'groups' => ['external-presence', 'external-presence-write']
  ]
)]
class ExternalPresence {
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
  #[ORM\Column]
  #[Groups(['external-presence-read'])]
  private ?int $id = null;

  #[ORM\Column(type: 'string', nullable: true)]
  #[Groups(['external-presence'])]
  private ?string $licence = null;

  #[ORM\Column(type: 'string')]
  #[Groups(['external-presence'])]
  private string $firstname;

  #[ORM\Column(type: 'string')]
  #[Groups(['external-presence'])]
  private string $lastname;

  #[Groups(['external-presence-read'])]
  private string $fullName = "";

  #[ORM\Column(type: Types::DATE_IMMUTABLE)]
  #[Groups(['external-presence-read'])]
  private ?\DateTimeImmutable $date = null;

  #[ORM\ManyToMany(targetEntity: Activity::class, inversedBy: 'externalPresences')]
  #[Groups(['external-presence'])]
  #[ActivityMustBeEnabled]
  private Collection $activities;

  #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
  #[Groups(['external-presence-read'])]
  private ?\DateTimeImmutable $createdAt = null;

  public function __construct() {
    $this->activities = new ArrayCollection();
    $this->createdAt = new \DateTimeImmutable();
    $this->date = new \DateTimeImmutable();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getLicence(): ?string {
    return $this->licence;
  }

  public function setLicence(?string $licence): ExternalPresence {
    $this->licence = $licence;
    return $this;
  }

  public function getFirstname(): string {
    return $this->firstname;
  }

  public function setFirstname(string $firstname): ExternalPresence {
    $this->firstname = ucfirst($firstname);
    return $this;
  }

  public function getLastname(): string {
    return $this->lastname;
  }

  public function setLastname(string $lastname): ExternalPresence {
    $this->lastname = strtoupper($lastname);
    return $this;
  }

  public function getFullName(): string {
    return $this->lastname . " " . $this->firstname;
  }

  public function getDate(): ?\DateTimeImmutable {
    return $this->date;
  }

  public function setDate(\DateTimeImmutable $date): static {
    $this->date = $date;
    return $this;
  }

  /**
   * @return Collection<int, Activity>
   */
  public function getActivities(): Collection {
    return $this->activities;
  }

  public function addActivity(Activity $activity): static {
    if (!$this->activities->contains($activity)) {
      $this->activities->add($activity);
    }

    return $this;
  }

  public function removeActivity(Activity $activity): static {
    $this->activities->removeElement($activity);

    return $this;
  }

  public function getCreatedAt(): ?\DateTimeImmutable {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeImmutable $createdAt): static {
    $this->createdAt = $createdAt;

    return $this;
  }
}