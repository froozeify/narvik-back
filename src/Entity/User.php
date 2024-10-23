<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model;
use App\Controller\UserPasswordReset;
use App\Controller\UserPasswordResetInitiate;
use App\Controller\UserSelf;
use App\Controller\UserSelfUpdatePassword;
use App\Entity\Abstract\UuidEntity;
use App\Enum\MemberRole;
use App\Filter\MultipleFilter;
use App\Repository\UserRepository;
use App\State\MemberProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'])]
#[ApiResource(
  operations: [
    new GetCollection(),
    new Get(),
    new Patch(),
    // No POST/DELETE since we import them


    new Get(
      uriTemplate: '/self',
      controller: UserSelf::class,
      openapi: new Model\Operation(
        summary: 'Return current logged user information',
      ),
      read: false
    ),
    new Put(
      uriTemplate: '/self/update-password',
      controller: UserSelfUpdatePassword::class,
      openapi: new Model\Operation(
        summary: 'Change current user password',
        requestBody: new Model\RequestBody(
          content: new \ArrayObject([
            'application/json' => [
              'schema' => [
                'type' => 'object',
                'properties' => [
                  'current' => ['type' => 'string'],
                  'new' => ['type' => 'string']
                ]
              ]
            ]
          ])
        )
      ),
      read: false,
      write: false
    ),

    new Post(
      uriTemplate: '/users/-/initiate-reset-password',
      controller: UserPasswordResetInitiate::class,
      openapi: new Model\Operation(
        summary: 'Trigger the reset password logic. Will send an email with a securityCode. Email must be enabled for this to work',
        requestBody: new Model\RequestBody(
          content: new \ArrayObject([
            'application/json' => [
              'schema' => [
                'type' => 'object',
                'properties' => [
                  'email' => ['type' => 'string'],
                ]
              ]
            ]
          ])
        ),
      ),
      read: false,
      deserialize: false,
      write: false,
      serialize: false,
    ),
    new Post(
      uriTemplate: '/users/-/reset-password',
      controller: UserPasswordReset::class,
      openapi: new Model\Operation(
        summary: 'Change the password for an user. If securityCode is invalid a new one will be sent.',
        requestBody: new Model\RequestBody(
          content: new \ArrayObject([
            'application/json' => [
              'schema' => [
                'type' => 'object',
                'properties' => [
                  'email' => ['type' => 'string'],
                  'password' => ['type' => 'string'],
                  'securityCode' => ['type' => 'string'],
                ]
              ]
            ]
          ])
        ),
      ),
      read: false,
      deserialize: false,
      write: false,
      serialize: false,
    ),

  ], normalizationContext: [
    'groups' => ['user', 'user-read']
  ], denormalizationContext: [
    'groups' => ['user', 'user-write']
  ],
  processor: MemberProcessor::class,
)]
#[ApiFilter(SearchFilter::class, properties: ['role' => 'exact'])]
#[ApiFilter(OrderFilter::class, properties: ['lastname' => 'ASC', 'firstname' => 'ASC'])]
#[ApiFilter(MultipleFilter::class, properties: ['firstname', 'lastname'])]
class User extends UuidEntity implements UserInterface, PasswordAuthenticatedUserInterface {
  // TODO: User Interface should move to User, also password fields && all, Member should be only member detail
  // A JOIN table should be create Between User <> Member, with in it also the role like that an user can be linked to multiple club and have different role

  #[ORM\Column(type: "string", nullable: true)]
  #[Assert\NotBlank(allowNull: true)]
  #[Groups(['user'])]
  private ?string $profileImage = null;

  #[Groups(['autocomplete', 'user-read', 'member-read', 'member-presence-read', 'sale-read'])]
  private ?string $fullName = null;

  #[ORM\Column]
  private array $roles = [];

  /**
   * @var string The hashed password
   */
  #[ORM\Column(nullable: true)]
  private ?string $password = null;

  /**
   * Not in database, use for update password
   * @var string|null
   */
  #[Groups(['super-admin-write'])]
  private ?string $plainPassword = null;

  #[ORM\Column]
  #[Groups(['user-read', 'super-admin-write'])]
  private bool $accountActivated = false;

  #[ORM\Column(length: 180, unique: true)]
  #[Groups(['user-read', 'super-admin-write'])]
  private ?string $email = null;

  #[ORM\Column(length: 255)]
  #[Groups(['autocomplete', 'user-read', 'admin-write'])]
  private ?string $firstname = null;

  #[ORM\Column(length: 255)]
  #[Groups(['autocomplete', 'user-read', 'admin-write'])]
  private ?string $lastname = null;

  public function getEmail(): ?string {
    return $this->email;
  }

  public function setEmail(string $email): static {
    if (empty($email)) {
      $email = null;
    }
    $this->email = $email;
    return $this;
  }

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUserIdentifier(): string {
    return $this->email;
  }

  /**
   * @see UserInterface
   */
  public function getRoles(): array {
    $roles = $this->roles;
    // We ensure every member has at least the ROLE_USER
    if (!in_array(MemberRole::badger->value, $roles)) {
      $roles[] = MemberRole::user->value;
    }
    return array_unique($roles);
  }

  public function setRoles(array $roles): static {
    $this->roles = $roles;
    return $this;
  }

  /**
   * @see PasswordAuthenticatedUserInterface
   */
  public function getPassword(): string {
    return $this->password;
  }

  public function setPassword(string $password): static {
    $this->password = $password;
    return $this;
  }

  public function getPlainPassword(): ?string {
    return $this->plainPassword;
  }

  public function setPlainPassword(?string $plainPassword): User {
    $this->plainPassword = $plainPassword;
    return $this;
  }

  /**
   * @see UserInterface
   */
  public function eraseCredentials(): void {
    // If you store any temporary, sensitive data on the user, clear it here
    $this->plainPassword = null;
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

  public function isAccountActivated(): bool {
    return $this->accountActivated;
  }

  public function setAccountActivated(bool $accountActivated): void {
    $this->accountActivated = $accountActivated;
  }
}
