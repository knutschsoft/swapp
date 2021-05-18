<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
#[ApiResource(
    collectionOperations: ["get"],
    itemOperations: ["get"],
    normalizationContext: ["groups" => ["user:read"]]
)]
class User implements UserInterface
{
    private const ROLE_DEFAULT = 'ROLE_USER';

    private const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /** @ORM\Column(type="string", length=180, unique=true) */
    protected string $email;

    /** @ORM\Column(type="boolean") */
    protected bool $enabled;

    /**
     * The salt to use for hashing.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $salt = null;

    /**
     * Encrypted password. Must be persisted.
     *
     * @ORM\Column(type="string")
     */
    protected string $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var ?string
     */
    protected ?string $plainPassword = null;

    /** @ORM\Column(type="datetime", name="last_login", nullable=true) */
    protected ?\DateTime $lastLogin = null;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @var ?string
     *
     * @ORM\Column(type="string", length=180, unique=true, name="confirmation_token", nullable=true)
     */
    protected ?string $confirmationToken = null;

    /** @ORM\Column(type="datetime", name="password_requested_at", nullable=true) */
    protected ?\DateTime $passwordRequestedAt = null;

    /**
     * @var string[]
     *
     * @ORM\Column(type="array")
     */
    protected array $roles;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /** @ORM\Column(type="string", length=180, unique=true) */
    private string $username;

    /**
     * @var Walk[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="Walk", inversedBy="walkTeamMembers")
     */
    private $walks;

    /**
     * @var Team[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Team", inversedBy="users")
     * @ORM\JoinTable(name="users_teams")
     */
    private $teams;

    public function __construct()
    {
        $this->username = '';
        $this->email = '';
        $this->enabled = false;
        $this->roles = [];
        $this->walks = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    public static function fromRegisterUserRequest(RegisterUserRequest $registerUserRequest, UserPasswordEncoderInterface $passwordEncoder): self
    {
        $instance = new self();
        $instance->email = strtolower($registerUserRequest->email);
        $instance->username = strtolower($registerUserRequest->username);
        $instance->changePassword($registerUserRequest->password, $passwordEncoder);
        $instance->addRole('ROLE_USER');
        $instance->disable();
        $instance->confirmationToken = Uuid::uuid4()->toString();

        return $instance;
    }


    public function changePassword(string $password, UserPasswordEncoderInterface $passwordEncoder): void
    {
        $this->password = $passwordEncoder->encodePassword($this, $password);
        $this->confirmationToken = null;
    }


    public static function createEmpty(): self
    {
        $instance = new self();
        $instance->username = '';
        $instance->email = '';
        $instance->enabled = false;
        $instance->roles = [];
        $instance->walks = new ArrayCollection();
        $instance->teams = new ArrayCollection();
        $instance->id = 0;

        return $instance;
    }

    /**
     * @return Team[]|Collection
     *
     * @Groups({"user:read"})
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    /** @param Team[]|Collection $teams */
    public function setTeams($teams): void
    {
        $this->teams = $teams;
        foreach ($teams as $team) {
            $team->addUser($this);
        }
    }

    /**
     * @return string
     *
     * @Groups({"user:read", "team:read"})
     */
    public function getId(): string
    {
        return (string) $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Walk[]|Collection
     *
     * @Groups({"user:read"})
     */
    public function getWalks(): Collection
    {
        return $this->walks;
    }

    /** @param Walk[]|Collection $walks */
    public function setWalks($walks): void
    {
        $this->walks = $walks;
    }

    public function hasTeam(Team $team): bool
    {
        return $this->teams->contains($team);
    }

    public function addTeam(Team $team): void
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->addUser($this);
        }
    }

    public function removeTeam(Team $team): void
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            $team->removeUser($this);
        }
    }

    public function addWalk(Walk $walk): void
    {
        $this->walks->add($walk);
    }

    public function removeWalk(Walk $walk): void
    {
        $this->walks->removeElement($walk);
    }

    public function addRole(string $role): void
    {
        $role = \strtoupper($role);
        if (static::ROLE_DEFAULT === $role) {
            return;
        }

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @return string
     *
     * @Groups({"user:read", "team:read"})
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getSalt(): string
    {
        return (string) $this->salt;
    }

    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    /**
     * @return string
     *
     * @Groups({"user:read", "team:read"})
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPlainPassword(): string
    {
        return (string) $this->plainPassword;
    }

    public function setPlainPassword(?string $password): void
    {
        $this->plainPassword = $password;
    }

    /**
     * @return \DateTime|null
     *
     * @Groups({"user:read", "team:read"})
     */
    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }

    public function updateLastLoginAt(): void
    {
        $this->lastLogin = new \DateTime();
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * @return string[]
     *
     * @Groups({"user:read", "team:read"})
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return \array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function hasRole(string $role): bool
    {
        return \in_array(\strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @return bool
     *
     * @Groups({"user:read", "team:read"})
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(string $boolean): void
    {
        $this->enabled = (bool) $boolean;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * @return bool
     *
     * @Groups({"user:read", "team:read"})
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    public function removeRole(string $role): void
    {
        $key = \array_search(\strtoupper($role), $this->roles, true);
        if (false !== $key) {
            unset($this->roles[$key]);
            $this->roles = \array_values($this->roles);
        }
    }

    public function setSuperAdmin(bool $boolean): void
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }
    }

    public function getPasswordRequestedAt(): ?\DateTime
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTime $date = null): void
    {
        $this->passwordRequestedAt = $date;
    }

    public function isPasswordRequestNonExpired(int $ttl): bool
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > \time();
    }

    public function __toString(): string
    {
        return \sprintf(
            '%s (%s)',
            $this->getUsername(),
            $this->getEmail()
        );
    }
}
