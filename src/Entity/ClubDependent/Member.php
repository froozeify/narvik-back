<?php

namespace App\Entity\ClubDependent;

use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
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
use App\Controller\ClubDependent\MemberChangeRole;
use App\Controller\ClubDependent\MemberImportFromItac;
use App\Controller\ClubDependent\MemberImportSecondaryClubFromItac;
use App\Controller\ClubDependent\MemberPhotosImportFromItac;
use App\Controller\ClubDependent\MemberSearchByLicenceOrName;
use App\Entity\Abstract\UuidEntity;
use App\Entity\Club;
use App\Entity\ClubDependent\Plugin\Presence\MemberPresence;
use App\Entity\ClubDependent\Plugin\Sale\Sale;
use App\Entity\File;
use App\Entity\Interface\ClubLinkedEntityInterface;
use App\Entity\Trait\SelfClubLinkedEntityTrait;
use App\Entity\UserMember;
use App\Enum\ClubRole;
use App\Filter\CurrentSeasonFilter;
use App\Filter\MultipleFilter;
use App\Repository\ClubDependent\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
#[ORM\Table(name: 'member')]
#[ApiResource(
  uriTemplate: '/clubs/{clubUuid}/members/{uuid}.{_format}',
  operations: [
    new GetCollection(
      uriTemplate: '/clubs/{clubUuid}/members.{_format}',
      uriVariables: [
        'clubUuid' => new Link(toProperty: 'club', fromClass: Club::class),
      ],
      security: "is_granted('".ClubRole::supervisor->value."', request)"
    ),
    new Post(
      uriTemplate: '/clubs/{clubUuid}/members.{_format}',
      uriVariables: [
        'clubUuid' => new Link(toProperty: 'club', fromClass: Club::class),
      ],
      securityPostDenormalize: "is_granted('".ClubRole::supervisor->value."', request)",
      read: false
    ),

    new Get(
      security: "is_granted('".ClubRole::supervisor->value."', object)",
    ),
    new Patch(
      security: "is_granted('".ClubRole::supervisor->value."', object)",
    ),
    new Delete(
      security: "is_granted('".ClubRole::admin->value."', object)"
    ),
    new Patch(
      uriTemplate: '/clubs/{clubUuid}/members/{uuid}/role',
      controller: MemberChangeRole::class,
      security: "is_granted('".ClubRole::admin->value."', object)",
    ),

    new Post(
      uriTemplate: '/clubs/{clubUuid}/members/-/search',
      uriVariables: [
        'clubUuid' => new Link(toProperty: 'club', fromClass: Club::class),
      ],
      controller: MemberSearchByLicenceOrName::class,
      openapi: new Model\Operation(
        summary: 'Search members matching the query (by licence or fullName)',
        requestBody: new Model\RequestBody(
          content: new \ArrayObject([
            'application/json' => [
              'schema' => [
                'type' => 'object',
                'properties' => [
                  'query' => [
                    'type' => 'string',
                  ]
                ]
              ]
            ]
          ])
        ),
      ),
      normalizationContext: ['groups' => 'autocomplete'],
      securityPostDenormalize: "is_granted('".ClubRole::supervisor->value."', request) || is_granted('".ClubRole::badger->value."', request)",
      read: false,
    ),
    new Post(
      uriTemplate: '/clubs/{clubUuid}/members/-/from-itac',
      uriVariables: [
        'clubUuid' => new Link(toProperty: 'club', fromClass: Club::class),
      ],
      controller: MemberImportFromItac::class,
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
      securityPostDenormalize: "is_granted('".ClubRole::admin->value."', request)",
      read: false,
      deserialize: false,
    ),
    new Post(
      uriTemplate: '/clubs/{clubUuid}/members/-/secondary-from-itac',
      uriVariables: [
        'clubUuid' => new Link(toProperty: 'club', fromClass: Club::class),
      ],
      controller: MemberImportSecondaryClubFromItac::class,
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
      securityPostDenormalize: "is_granted('".ClubRole::admin->value."', request)",
      read: false,
      deserialize: false,
    ),
    new Post(
      uriTemplate: '/clubs/{clubUuid}/members/-/photos-from-itac',
      uriVariables: [
        'clubUuid' => new Link(toProperty: 'club', fromClass: Club::class),
      ],
      controller: MemberPhotosImportFromItac::class,
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
      securityPostDenormalize: "is_granted('".ClubRole::admin->value."', request)",
      read: false,
      deserialize: false,
    )
  ],

  uriVariables: [
    'clubUuid' => new Link(toProperty: 'club', fromClass: Club::class),
    'uuid' => new Link(fromClass: self::class),
  ], normalizationContext: [
    'groups' => ['member', 'member-read', 'common-read']
  ], denormalizationContext: [
    'groups' => ['member', 'member-write']
  ],
)]
#[ApiFilter(ExistsFilter::class, properties: ['licence'])]
#[ApiFilter(SearchFilter::class, properties: ['role' => 'exact'])]
#[ApiFilter(OrderFilter::class, properties: ['lastname' => 'ASC', 'firstname' => 'ASC'])]
#[ApiFilter(MultipleFilter::class, properties: ['firstname', 'lastname', 'licence'])]
#[ApiFilter(CurrentSeasonFilter::class, properties: ['memberSeasons.season'])]
class Member extends UuidEntity implements ClubLinkedEntityInterface {
  use SelfClubLinkedEntityTrait;

  private bool $skipAutoSetUserMember = false;

  /**
   * @var Collection<int, Sale>
   */
  #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Sale::class)]
  private Collection $sales;

  #[ORM\OneToOne(targetEntity: File::class)]
  #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
  #[Groups(['member-read', 'self-read', 'member-presence-read'])]
  private ?File $profileImage = null;

  #[Groups(['member-read', 'member-presence-read'])]
  private ?\DateTimeImmutable $lastControlShooting = null;

  #[Groups(['member-read', 'member-presence-read'])]
  private ?MemberSeason $currentSeason = null;

  #[Groups(['member-read', 'member-presence-read'])]
  private ClubRole|null $role = null;

  #[Groups(['autocomplete', 'self-read', 'member-read', 'member-presence-read', 'sale-read'])]
  private ?string $fullName = null;

  #[ORM\OneToOne(mappedBy: 'member', targetEntity: UserMember::class)]
  private ?UserMember $userMember = null;

  #[ORM\OneToMany(mappedBy: 'member', targetEntity: MemberPresence::class, orphanRemoval: true)]
  private Collection $memberPresences;

  #[ORM\OneToMany(mappedBy: 'member', targetEntity: MemberSeason::class, orphanRemoval: true)]
  private Collection $memberSeasons;




  //// ITAC CSV FIELDS ////




  #[ORM\Column(length: 180, nullable: true)]
  #[Groups(['member-read', 'club-supervisor-write'])]
  #[Assert\NotBlank()]
  private ?string $email = null;

  #[ORM\Column(length: 10, unique: true, nullable: true)]
  #[Groups(['autocomplete', 'member-read', 'club-supervisor-write', 'member-presence-read'])]
  #[Assert\Regex(pattern: '/\d{8,10}/')]
  private ?string $licence = null;

  #[ORM\Column(length: 255)]
  #[Groups(['autocomplete', 'member-read', 'club-supervisor-write'])]
  #[Assert\NotBlank()]
  private ?string $firstname = null;

  #[ORM\Column(length: 255)]
  #[Groups(['autocomplete', 'member-read', 'club-supervisor-write'])]
  #[Assert\NotBlank()]
  private ?string $lastname = null;

  #[ORM\Column(length: 1)]
  #[Groups(['member-read', 'club-supervisor-write'])]
  #[Assert\Choice(['M', 'F'])]
  private string $gender = "M";

  #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
  #[Groups(['member-read', 'club-supervisor-write'])]
  private ?\DateTimeInterface $birthday = null;

  #[ORM\Column]
  #[Groups(['member-read', 'club-admin-write'])]
  private bool $handisport = false;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups(['member-read', 'club-supervisor-write'])]
  private ?string $postal1 = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups(['member-read', 'club-supervisor-write'])]
  private ?string $postal2 = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups(['member-read', 'club-supervisor-write'])]
  private ?string $postal3 = null;

  #[ORM\Column(nullable: true)]
  #[Groups(['member-read', 'club-supervisor-write'])]
  private ?int $zipCode = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups(['member-read', 'club-supervisor-write'])]
  private ?string $city = null;

  #[ORM\Column(length: 255, nullable: true)]
  #[Groups(['member-read', 'club-supervisor-write'])]
  private ?string $country = null;

  #[ORM\Column(length: 14, nullable: true)]
  #[Groups(['member-read', 'club-supervisor-write'])]
  private ?string $phone = null;

  #[ORM\Column(length: 14, nullable: true)]
  #[Groups(['member-read', 'club-supervisor-write'])]
  private ?string $mobilePhone = null;

  #[ORM\Column]
  #[Groups(['club-admin-read', 'club-admin-write'])]
  private bool $blacklisted = false;

  public function __construct() {
    parent::__construct();
    $this->memberPresences = new ArrayCollection();
    $this->memberSeasons = new ArrayCollection();
    $this->sales = new ArrayCollection();
  }

  public function getProfileImage(): ?File {
    return $this->profileImage;
  }

  public function setProfileImage(?File $profileImage): Member {
    $this->profileImage = $profileImage;
    return $this;
  }

  public function getEmail(): ?string {
    return $this->email;
  }

  public function setEmail(?string $email): static {
    if (empty($email)) {
      $email = null;
    }
    $this->email = $email;
    return $this;
  }

  public function getLicence(): ?string {
    return $this->licence;
  }

  public function setLicence(?string $licence): static {
    $this->licence = $licence;
    return $this;
  }

  public function getFirstname(): ?string {
    return $this->firstname;
  }

  public function setFirstname(string $firstname): static {
    $this->firstname = ucfirst($firstname);
    return $this;
  }

  public function getLastname(): ?string {
    return $this->lastname;
  }

  public function setLastname(string $lastname): static {
    $this->lastname = strtoupper($lastname);
    return $this;
  }

  public function getFullName(): ?string {
    return $this->lastname . " " . $this->firstname;
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
      $memberPresence->setMember($this);
    }

    return $this;
  }

  public function removeMemberPresence(MemberPresence $memberPresence): static {
    if ($this->memberPresences->removeElement($memberPresence)) {
      // set the owning side to null (unless already changed)
      if ($memberPresence->getMember() === $this) {
        $memberPresence->setMember(null);
      }
    }
    return $this;
  }

  public function getGender(): string {
    return $this->gender;
  }

  public function setGender(string $gender): Member {
    $this->gender = $gender;
    return $this;
  }

  public function getBirthday(): ?\DateTimeInterface {
    return $this->birthday;
  }

  public function setBirthday(?\DateTimeInterface $birthday): Member {
    $this->birthday = $birthday;
    return $this;
  }

  public function isHandisport(): bool {
    return $this->handisport;
  }

  public function setHandisport(bool $handisport): Member {
    $this->handisport = $handisport;
    return $this;
  }

  public function getPostal1(): ?string {
    return $this->postal1;
  }

  public function setPostal1(?string $postal1): Member {
    $this->postal1 = $postal1;
    return $this;
  }

  public function getPostal2(): ?string {
    return $this->postal2;
  }

  public function setPostal2(?string $postal2): Member {
    $this->postal2 = $postal2;
    return $this;
  }

  public function getPostal3(): ?string {
    return $this->postal3;
  }

  public function setPostal3(?string $postal3): Member {
    $this->postal3 = $postal3;
    return $this;
  }

  public function getZipCode(): ?int {
    return $this->zipCode;
  }

  public function setZipCode(?int $zipCode): Member {
    $this->zipCode = $zipCode;
    return $this;
  }

  public function getCity(): ?string {
    return $this->city;
  }

  public function setCity(?string $city): Member {
    $this->city = $city;
    return $this;
  }

  public function getCountry(): ?string {
    return $this->country;
  }

  public function setCountry(?string $country): Member {
    $this->country = $country;
    return $this;
  }

  public function getPhone(): ?string {
    return $this->phone;
  }

  public function setPhone(?string $phone): Member {
    $this->phone = $phone;
    return $this;
  }

  public function getMobilePhone(): ?string {
    return $this->mobilePhone;
  }

  public function setMobilePhone(?string $mobilePhone): Member {
    $this->mobilePhone = $mobilePhone;
    return $this;
  }

  public function isBlacklisted(): bool {
    return $this->blacklisted;
  }

  public function setBlacklisted(bool $blacklisted): Member {
    $this->blacklisted = $blacklisted;
    return $this;
  }

  /**
   * @return Collection<int, MemberSeason>
   */
  public function getMemberSeasons(): Collection {
    return $this->memberSeasons;
  }

  public function addMemberSeason(MemberSeason $memberSeason): Member {
    if (!$this->memberSeasons->contains($memberSeason)) {
      $this->memberSeasons->add($memberSeason);
      $memberSeason->setMember($this);
    }

    return $this;
  }

  public function removeMemberSeason(MemberSeason $memberSeason): Member {
    if ($this->memberSeasons->removeElement($memberSeason)) {
      // set the owning side to null (unless already changed)
      if ($memberSeason->getMember() === $this) {
        $memberSeason->setMember(null);
      }
    }

    return $this;
  }

  public function getLastControlShooting(): ?\DateTimeImmutable {
    return $this->lastControlShooting;
  }

  public function setLastControlShooting(?\DateTimeImmutable $lastControlShooting): void {
    $this->lastControlShooting = $lastControlShooting;
  }

  public function getCurrentSeason(): ?MemberSeason {
    return $this->currentSeason;
  }

  public function setCurrentSeason(?MemberSeason $currentSeason): Member {
    $this->currentSeason = $currentSeason;
    return $this;
  }

  /**
   * @return Collection<int, Sale>
   */
  public function getSales(): Collection {
    return $this->sales;
  }

  public function addSale(Sale $sale): static {
    if (!$this->sales->contains($sale)) {
      $this->sales->add($sale);
      $sale->setSeller($this);
    }
    return $this;
  }

  public function removeSale(Sale $sale): static {
    if ($this->sales->removeElement($sale)) {
      // set the owning side to null (unless already changed)
      if ($sale->getSeller() === $this) {
        $sale->setSeller(null);
      }
    }
    return $this;
  }

  public function getUserMember(): ?UserMember {
    return $this->userMember;
  }

  public function setUserMember(?UserMember $userMember): Member {
    $this->userMember = $userMember;
    return $this;
  }

  public function getRole(): ?ClubRole {
    return $this->getUserMember()?->getRole();
  }

  public function isSkipAutoSetUserMember(): bool {
    return $this->skipAutoSetUserMember;
  }

  public function setSkipAutoSetUserMember(bool $skipAutoSetUserMember): Member {
    $this->skipAutoSetUserMember = $skipAutoSetUserMember;
    return $this;
  }
}
