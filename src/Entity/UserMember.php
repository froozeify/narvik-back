<?php

namespace App\Entity;

use App\Enum\ClubRole;
use App\Repository\UserMemberRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserMemberRepository::class)]
#[UniqueEntity(fields: ['member', 'user'], message: 'Member already linked')]
class UserMember {
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(inversedBy: 'memberships')]
  #[ORM\JoinColumn(nullable: false)]
  #[Groups(['member-read', 'admin-write'])]
  private ?User $user = null;

  #[ORM\OneToOne(cascade: ['persist', 'remove'])]
  #[ORM\JoinColumn(nullable: false)]
  #[Groups(['member-read', 'admin-write'])]
  private ?Member $member = null;

  #[ORM\Column(type: "string", enumType: ClubRole::class)]
  #[Groups(['member-read', 'admin-write'])]
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

  public function getRole(): ClubRole {
    return $this->role;
  }

  public function setRole(ClubRole $role): UserMember {
    $this->role = $role;
    return $this;
  }
}
