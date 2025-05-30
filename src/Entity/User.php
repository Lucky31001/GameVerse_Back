<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\ChangePasswordController;
use App\Controller\LoginController;
use App\Controller\LogoutController;
use App\Controller\MeController;
use App\Controller\RegisterController;
use App\Dto\ChangePasswordDTO;
use App\Dto\LoginUserDTO;
use App\Dto\RegisterUserDTO;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['user:collection:read']],
        ),
        new Post(),
        new Get(
            normalizationContext: ['groups' => ['user:item:read']],
        ),
        new Post(
            uriTemplate: '/auth/register',
            controller: RegisterController::class,
            input: RegisterUserDTO::class
        ),
        new Post(
            uriTemplate:  '/auth/login',
            controller: LoginController::class,
            input: LoginUserDTO::class
        ),
        new Delete(
            uriTemplate: '/auth/logout',
            controller: LogoutController::class,
            security: 'is_granted("IS_AUTHENTICATED_FULLY")',
            securityMessage: 'You must be logged in to access this resource.',
        ),
        new Get(
            uriTemplate: '/me',
            controller: MeController::class,
            security: 'is_granted("IS_AUTHENTICATED_FULLY")',
            securityMessage: 'You must be logged in to access this resource.',
            read: false
        ),
        new Post(
            uriTemplate: '/auth/change-password',
            controller: ChangePasswordController::class,
            security: 'is_granted("IS_AUTHENTICATED_FULLY")',
            securityMessage: 'You must be logged in to access this resource.',
            input: ChangePasswordDTO::class,
        ),
    ],
)]
#[ApiResource]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[Groups(groups: ['user:item:read', 'user:collection:read'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[Groups(groups: ['user:collection:read'])]
    #[ORM\Column]
    private array $roles = [];

    #[Groups(groups: ['user:item:read', 'user:collection:read'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->roles[] = 'ROLE_USER';
    }
    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
