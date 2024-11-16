<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Controller\ExternalPresenceToday;
use App\Entity\Abstract\UuidEntity;
use App\Entity\ClubDependent\Activity;
use App\Entity\Interface\TimestampEntityInterface;
use App\Entity\Trait\TimestampTrait;
use App\Filter\MultipleFilter;
use App\Repository\ExternalPresenceRepository;
use App\Validator\Constraints\ActivityMustBeEnabled;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ExternalPresenceRepository::class)]
#[UniqueEntity(fields: ['licence', 'date'], message: 'Member already registered for that day')]
#[ApiResource(
  operations: [
    new GetCollection(),
    new Post(),
    new Patch(),
    new Delete(),

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
#[ApiFilter(DateFilter::class, properties: ['date' => DateFilter::EXCLUDE_NULL])]
#[ApiFilter(OrderFilter::class, properties: ['date' => 'DESC', 'createdAt' => 'DESC'])]
#[ApiFilter(MultipleFilter::class, properties: ['firstname', 'lastname', 'licence'])]
class ExternalPresence extends UuidEntity implements TimestampEntityInterface {
  use TimestampTrait;

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

  public function __construct() {
    parent::__construct();
    $this->activities = new ArrayCollection();
    $this->date = new \DateTimeImmutable();
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
}
