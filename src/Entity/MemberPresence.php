<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
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
use App\Controller\MemberPresencesFromItac;
use App\Controller\MemberPresenceToday;
use App\Repository\MemberPresenceRepository;
use App\Validator\Constraints\ActivityMustBeEnabled;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MemberPresenceRepository::class)]
#[UniqueEntity(fields: ['member', 'date'], message: 'Member already registered for today')]
#[ApiResource(
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch(),
    new Delete(
      security: "is_granted('ROLE_ADMIN')"
    ),

    new GetCollection(
      uriTemplate: '/member-presences/-/today',
      controller: MemberPresenceToday::class,
      openapi: new Model\Operation(
        summary: 'Get all members present today',
      ),
      read: false,
      write: false
    ),

    new Post(
      uriTemplate: '/member-presences/-/from-cerbere',
      controller: MemberPresencesFromItac::class,
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
  ],
  normalizationContext: [
    'groups' => ['member-presence', 'member-presence-read']
  ],
  denormalizationContext: [
    'groups' => ['member-presence', 'member-presence-write']
  ]
)]
#[ApiResource(
  uriTemplate: '/members/{memberId}/presences.{_format}',
  operations: [
    new GetCollection(),
  ], uriVariables: [
    'memberId' => new Link(fromClass: Member::class, toProperty: 'member'),
  ], normalizationContext: [
    'groups' => ['member-presence', 'member-presence-read']
  ],
  paginationClientEnabled: true,
)]
#[ApiFilter(DateFilter::class, properties: ['date' => DateFilter::EXCLUDE_NULL])]
#[ApiFilter(OrderFilter::class, properties: ['date' => 'DESC'])]
class MemberPresence {
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
  #[ORM\Column]
  #[Groups(['member-presence-read'])]
  private ?int $id = null;

  #[ORM\ManyToOne(targetEntity: Member::class, inversedBy: 'memberPresences')]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
  #[Groups(['member-presence'])]
  private ?Member $member = null;

  #[ORM\Column(type: Types::DATE_IMMUTABLE)]
  #[Groups(['member-presence-read'])]
  private ?\DateTimeImmutable $date = null;

  #[ORM\ManyToMany(targetEntity: Activity::class, inversedBy: 'memberPresences')]
  #[Groups(['member-presence'])]
  #[ActivityMustBeEnabled]
  private Collection $activities;

  #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
  #[Groups(['member-presence-read'])]
  private ?\DateTimeImmutable $createdAt = null;

  public function __construct() {
    $this->activities = new ArrayCollection();
    $this->createdAt = new \DateTimeImmutable();
    $this->date = new \DateTimeImmutable();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getMember(): ?Member {
    return $this->member;
  }

  public function setMember(?Member $member): static {
    $this->member = $member;

    return $this;
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
