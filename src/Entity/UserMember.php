<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\Entity\ClubDependent\Member;
use App\Enum\ClubRole;
use App\Repository\UserMemberRepository;
use App\State\UserMemberProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserMemberRepository::class)]
#[UniqueEntity(fields: ['member'], message: 'Member already linked')]
#[ApiResource(
  operations: [
    new Get(),
    new Patch(),
    new Delete(),
  ], normalizationContext: [
    'groups' => ['user-member', 'user-member-read']
  ], denormalizationContext: [
    'groups' => ['user-member', 'user-member-write']
  ],
  processor: UserMemberProcessor::class,
)]
class UserMember {
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(inversedBy: 'memberships')]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
  #[Groups(['user-member-read', 'club-admin-write'])]
  private ?User $user = null;

  #[ORM\OneToOne()]
  #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
  #[Groups(['user-member-read', 'club-admin-write'])]
  private ?Member $member = null;

  #[ORM\ManyToOne(targetEntity: Club::class)]
  #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
  private ?Club $badgerClub = null;

  #[ORM\Column(type: "string", enumType: ClubRole::class)]
  #[Groups(['user-member-read', 'club-admin-write'])]
  private ClubRole $role = ClubRole::member;

  public function getId(): ?int {
    return $this->id;
  }

  public function getUser(): ?User {
    return $this->user;
  }

  public function setUser(?User $user): static {
    $this->user = $user;
    return $this;
  }

  public function getMember(): ?Member {
    return $this->member;
  }

  public function setMember(Member $member): static {
    $this->member = $member;
    return $this;
  }

  public function getBadgerClub(): ?Club {
    return $this->badgerClub;
  }

  public function setBadgerClub(?Club $club): static {
    $this->badgerClub = $club;
    return $this;
  }

  public function getRole(): ClubRole {
    return $this->role;
  }

  public function setRole(ClubRole $role): UserMember {
    $this->role = $role;
    return $this;
  }
}
