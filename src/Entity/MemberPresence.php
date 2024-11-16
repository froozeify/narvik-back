<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Controller\MemberPresencesFromCsv;
use App\Controller\MemberPresencesFromItac;
use App\Controller\MemberPresencesImportFromExternal;
use App\Controller\MemberPresenceToday;
use App\Entity\Abstract\UuidEntity;
use App\Entity\ClubDependent\Activity;
use App\Entity\ClubDependent\Member;
use App\Entity\Interface\TimestampEntityInterface;
use App\Entity\Trait\TimestampTrait;
use App\Filter\MultipleFilter;
use App\Repository\MemberPresenceRepository;
use App\Validator\Constraints\ActivityMustBeEnabled;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MemberPresenceRepository::class)]
#[UniqueEntity(fields: ['member', 'date'], message: 'Member already registered for that day')]
#[ApiResource(
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch(),
    new Delete(),

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
    ),
    new Post(
      uriTemplate: '/member-presences/-/from-csv',
      controller: MemberPresencesFromCsv::class,
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
    ),
    new Post(
      uriTemplate: '/member-presences/-/import-from-external-presences',
      controller: MemberPresencesImportFromExternal::class,
      security: "is_granted('ROLE_ADMIN')",
      deserialize: false,
    )
  ],
  normalizationContext: [
    'groups' => ['member-presence', 'member-presence-read']
  ],
  denormalizationContext: [
    'groups' => ['member-presence', 'member-presence-write']
  ],
  paginationClientEnabled: true,
)]
#[ApiResource(
  uriTemplate: '/members/{memberId}/presences.{_format}',
  operations: [
    new GetCollection(),
  ], uriVariables: [
    'memberId' => new Link(toProperty: 'member', fromClass: Member::class),
  ], normalizationContext: [
    'groups' => ['member-presence', 'member-presence-read']
  ],
  paginationClientEnabled: true,
)]
#[ApiFilter(DateFilter::class, properties: ['date' => DateFilter::EXCLUDE_NULL])]
#[ApiFilter(OrderFilter::class, properties: ['date' => 'DESC', 'createdAt' => 'DESC'])]
#[ApiFilter(MultipleFilter::class, properties: ['member.firstname', 'member.lastname', 'member.licence'])]
#[ApiFilter(SearchFilter::class, properties: ['activities.uuid' => 'exact'])]
class MemberPresence extends UuidEntity implements TimestampEntityInterface {
  use TimestampTrait;

  #[ORM\ManyToOne(targetEntity: Member::class, inversedBy: 'memberPresences')]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
  #[Groups(['member-presence'])]
  private ?Member $member = null;

  #[ORM\Column(type: Types::DATE_IMMUTABLE)]
  #[Groups(['member-presence'])]
  private ?\DateTimeImmutable $date = null;

  #[ORM\ManyToMany(targetEntity: Activity::class, inversedBy: 'memberPresences')]
  #[Groups(['member-presence'])]
  #[ActivityMustBeEnabled]
  private Collection $activities;

  public function __construct() {
    parent::__construct();
    $this->activities = new ArrayCollection();
    $this->date = new \DateTimeImmutable();
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

}
