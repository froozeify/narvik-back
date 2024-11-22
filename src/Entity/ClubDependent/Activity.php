<?php

namespace App\Entity\ClubDependent;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model;
use App\Controller\ActivityMergeTo;
use App\Controller\InventoryCategoryMove;
use App\Entity\Abstract\UuidEntity;
use App\Entity\Club;
use App\Entity\ExternalPresence;
use App\Entity\Interface\ClubLinkedEntityInterface;
use App\Entity\MemberPresence;
use App\Entity\Trait\SelfClubLinkedEntityTrait;
use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[ApiResource(
  operations: [
    new Get(),
    new Post(
      securityPostDenormalize: "is_granted('CLUB_ADMIN', object)"
    ),
    new Patch(
      security: "is_granted('CLUB_ADMIN', object)"
    ),
    new Delete(
      security: "is_granted('CLUB_ADMIN', object)"
    ),

    new Post(
      uriTemplate: '/activities/{uuid}/merge-to/{targetUuid}',
      controller: ActivityMergeTo::class,
      openapi: new Model\Operation(
        summary: 'Merge (and then delete) with another activity',
        parameters: [
          new Model\Parameter('targetActivity', 'path', 'Activity to be merged into', true)
        ],
        requestBody: new Model\RequestBody('Must be an empty json body')
      ),

      security: "is_granted('CLUB_ADMIN')",
      read: false,
      write: false,
    )
  ],
  normalizationContext: [
    'groups' => ['activity', 'activity-read']
  ],
  denormalizationContext: [
    'groups' => ['club-admin-write']
  ],
  order: ['name' => 'asc'],
)]
#[ApiResource(
  uriTemplate: '/clubs/{clubUuid}/activities.{_format}',
  operations: [
    new GetCollection(),
  ],
  uriVariables: [
    'clubUuid' => new Link(toProperty: 'club', fromClass: Club::class),
  ],
  normalizationContext: [
    'groups' => ['activity', 'activity-read']
  ],
  order: ['name' => 'asc'],
)]
#[ApiFilter(OrderFilter::class, properties: ['name' => 'ASC', 'isEnabled' => 'ASC'])]
class Activity extends UuidEntity implements ClubLinkedEntityInterface {
  use SelfClubLinkedEntityTrait;

  #[ORM\Column(length: 255)]
  #[Groups(['club-admin-write', 'activity-read','member-read', 'member-presence', 'external-presence'])]
  #[Assert\NotBlank()]
  private string $name;

  #[ORM\Column(type: 'boolean')]
  #[Groups(['club-admin-write', 'activity-read','member-read', 'member-presence', 'external-presence'])]
  private ?bool $isEnabled = true;

  #[ORM\ManyToMany(targetEntity: MemberPresence::class, mappedBy: 'activities')]
  private Collection $memberPresences;

  #[ORM\ManyToMany(targetEntity: ExternalPresence::class, mappedBy: 'activities')]
  private Collection $externalPresences;

  public function __construct() {
    parent::__construct();
    $this->memberPresences = new ArrayCollection();
    $this->externalPresences = new ArrayCollection();
  }

  public function getName(): string {
    return $this->name;
  }

  public function setName(string $name): static {
    $this->name = trim($name);
    return $this;
  }

  public function getIsEnabled(): ?bool {
    return $this->isEnabled;
  }

  public function setIsEnabled(?bool $isEnabled): Activity {
    $this->isEnabled = $isEnabled;
    return $this;
  }

  /**
   * @return Collection<int, MemberPresence>
   */
  public function getMemberPresences(): Collection {
    return $this->memberPresences;
  }

  public function addMemberPresence(MemberPresence $memberPresence): static {
    if (!$this->memberPresences->contains($memberPresence)) {
      $this->memberPresences->add($memberPresence);
      $memberPresence->addActivity($this);
    }
    return $this;
  }

  public function removeMemberPresence(MemberPresence $memberPresence): static {
    if ($this->memberPresences->removeElement($memberPresence)) {
      $memberPresence->removeActivity($this);
    }
    return $this;
  }

  /**
   * @return Collection<int, ExternalPresence>
   */
  public function getExternalPresences(): Collection {
    return $this->externalPresences;
  }

  public function addExternalPresence(ExternalPresence $externalPresence): static {
    if (!$this->externalPresences->contains($externalPresence)) {
      $this->externalPresences->add($externalPresence);
      $externalPresence->addActivity($this);
    }
    return $this;
  }

  public function removeExternalPresence(ExternalPresence $externalPresence): static {
    if ($this->externalPresences->removeElement($externalPresence)) {
      $externalPresence->removeActivity($this);
    }
    return $this;
  }
}
