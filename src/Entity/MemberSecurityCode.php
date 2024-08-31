<?php

namespace App\Entity;

use App\Entity\Interface\TimestampEntityInterface;
use App\Entity\Trait\TimestampTrait;
use App\Enum\MemberSecurityCodeTrigger;
use App\Repository\MemberSecurityCodeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MemberSecurityCodeRepository::class)]
class MemberSecurityCode implements TimestampEntityInterface {
  use TimestampTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 10)]
  private string $code;

  #[ORM\ManyToOne(targetEntity: Member::class)]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
  private ?Member $member = null;

  #[ORM\Column(type: "string", enumType: MemberSecurityCodeTrigger::class)]
  private ?MemberSecurityCodeTrigger $trigger = null;

  #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
  private \DateTimeImmutable $expireAt;

  public function __construct() {
    $this->code = strtoupper(bin2hex(random_bytes(10)));
    $this->expireAt = new \DateTimeImmutable('+ 10 minutes');
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getCode(): string {
    return $this->code;
  }

  public function setCode(string $code): static {
    $this->code = $code;
    return $this;
  }

  public function getMember(): ?Member {
    return $this->member;
  }

  public function setMember(?Member $member): static {
    $this->member = $member;
    return $this;
  }

  public function getTrigger(): ?MemberSecurityCodeTrigger {
    return $this->trigger;
  }

  public function setTrigger(MemberSecurityCodeTrigger $trigger): static {
    $this->trigger = $trigger;
    return $this;
  }

  public function getExpireAt(): \DateTimeImmutable {
    return $this->expireAt;
  }

  public function setExpireAt(\DateTimeImmutable $expireAt): static {
    $this->expireAt = $expireAt;
    return $this;
  }
}
