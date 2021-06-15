<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Dto\User\IsConfirmationTokenValidRequest;
use App\Dto\User\PasswordChangeRequest;
use App\Dto\User\RequestPasswordResetRequest;
use App\Dto\User\UserChangeRequest;
use App\Dto\User\UserCreateRequest;
use App\Dto\User\UserEmailConfirmRequest;
use App\Security\Voter\ClientVoter;
use App\Security\Voter\UserVoter;
use App\Value\ConfirmationToken;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
#[ApiResource(
    collectionOperations: [
        "get",
        "request_password_reset" => [
            "messenger" => "input",
            "input" => RequestPasswordResetRequest::class,
            "output" => false,
            "method" => "post",
            "status" => 200,
            "path" => "/users/request-password-reset",
            "openapi_context" => [
                "summary" => "An user requests a new password.",
            ],
        ],
        "is_confirmation_token_valid" => [
            "messenger" => "input",
            "input" => IsConfirmationTokenValidRequest::class,
            "output" => User::class,
            "method" => "post",
            "status" => 200,
            "path" => "/users/is-confirmation-token-valid",
            "openapi_context" => [
                "summary" => "Check if an user is allowed to perform a certain action.",
            ],
        ],
        "user_email_confirm" => [
            "messenger" => "input",
            "input" => UserEmailConfirmRequest::class,
            "output" => User::class,
            "method" => "post",
            "status" => 200,
            "path" => "/users/user-email-confirm",
            "openapi_context" => [
                "summary" => "Enables an user and sends password request notification.",
            ],
        ],
        "change_password" => [
            "messenger" => "input",
            "input" => PasswordChangeRequest::class,
            "output" => User::class,
            "method" => "post",
            "status" => 200,
            "path" => "/users/change-password",
            "openapi_context" => [
                "summary" => "Change password of an user.",
            ],
        ],
        "change_user" => [
            "messenger" => "input",
            "input" => UserChangeRequest::class,
            "output" => User::class,
            "method" => "post",
            "status" => 200,
            "path" => "/users/change",
            "openapi_context" => [
                "summary" => "Change attributes of an user.",
            ],
            "security_post_denormalize"
                => "is_granted('".UserVoter::EDIT."', object.user) and (is_granted('ROLE_SUPER_ADMIN') or not object.superAdminRightsNeeded())",
        ],
        "create_user" => [
            "messenger" => "input",
            "input" => UserCreateRequest::class,
            "output" => User::class,
            "method" => "post",
            "status" => 200,
            "path" => "/users/create",
            "openapi_context" => [
                "summary" => "Create an user which is initially disabled. Will send notification with further instructions.",
            ],
            "security_post_denormalize" =>
                "(is_granted('ROLE_SUPER_ADMIN') or not object.superAdminRightsNeeded()) and ".
                 "is_granted('".ClientVoter::READ."', object.client)"
            ,
        ],
    ],
    itemOperations: ["get"],
    normalizationContext: ["groups" => ["user:read"]]
)]
class User implements UserInterface
{
    private const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const ROLE_ALLOWED_TO_SWITCH = 'ROLE_ALLOWED_TO_SWITCH';
    public const ROLES = [
        self::ROLE_DEFAULT,
        self::ROLE_ADMIN,
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ALLOWED_TO_SWITCH,
    ];

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

    protected ?string $plainPassword = null;

    /** @ORM\Column(type="datetime", name="last_login", nullable=true) */
    protected ?\DateTime $lastLogin = null;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @ORM\Embedded(class="App\Value\ConfirmationToken", columnPrefix=false)
     */
    protected ConfirmationToken $confirmationToken;

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
     * @var Collection<int, Walk>
     *
     * @ORM\ManyToMany(targetEntity="Walk", inversedBy="walkTeamMembers")
     */
    private Collection $walks;

    /**
     * @var Collection<int, Team>
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Team", inversedBy="users")
     * @ORM\JoinTable(name="users_teams")
     */
    private Collection $teams;

    /** @ORM\ManyToOne(targetEntity="Client", inversedBy="users") */
    private Client $client;

    public function __construct()
    {
        $this->username = '';
        $this->email = '';
        $this->enabled = false;
        $this->roles = [];
        $this->walks = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->confirmationToken = ConfirmationToken::createEmpty();
    }

    public static function fromUserCreateRequest(UserCreateRequest $request, UserPasswordEncoderInterface $passwordEncoder): self
    {
        $instance = new self();
        $instance->email = \strtolower($request->email);
        $instance->username = \strtolower($request->username);
        $instance->changePassword(\md5((string) \time(), false), $passwordEncoder);
        $instance->client = $request->client;
        $instance->refreshConfirmationToken();
        $instance->setRoles($request->roles);
        $instance->disable();

        return $instance;
    }

    public function changePassword(string $password, UserPasswordEncoderInterface $passwordEncoder): void
    {
        $this->password = $passwordEncoder->encodePassword($this, $password);
        $this->confirmationToken = ConfirmationToken::createEmpty();
        $this->passwordRequestedAt = null;
    }

    /**
     * @return Collection<int, Team>
     *
     * @Groups({"user:read"})
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    /** @param Collection<int,Team> $teams */
    public function setTeams(Collection $teams): void
    {
        $this->teams = $teams;
        foreach ($teams as $team) {
            $team->addUser($this);
        }
    }

    /**
     * @return int
     *
     * @Groups({"user:read", "team:read"})
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Collection<int,Walk>
     *
     * @Groups({"user:read"})
     */
    public function getWalks(): Collection
    {
        return $this->walks;
    }

    /** @param Collection<int, Walk> $walks */
    public function setWalks(Collection $walks): void
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
        if (static::ROLE_SUPER_ADMIN === $role) {
            $this->addRole(static::ROLE_ALLOWED_TO_SWITCH);
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

    public function getConfirmationToken(): ConfirmationToken
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(ConfirmationToken $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function refreshConfirmationToken(): void
    {
        $this->confirmationToken = ConfirmationToken::create();
        ConfirmationToken::create();
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
        if (\in_array(\strtoupper(static::ROLE_SUPER_ADMIN), $roles, true)) {
            $roles[] = static::ROLE_ALLOWED_TO_SWITCH;
        }

        return \array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }
        $this->addRole(static::ROLE_DEFAULT);
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

    #[Groups(['user:read', 'team:read'])]
    public function getClient(): Client
    {
        return $this->client;
    }

    public function updateClient(Client $client): void
    {
        $this->client = $client;
    }

    public function __toString(): string
    {
        return \sprintf(
            '%s (%s)',
            $this->getUsername(),
            $this->getEmail()
        );
    }

    public function requestPassword(): void
    {
        $this->refreshConfirmationToken();
        $this->passwordRequestedAt = new \DateTime();
    }
}
