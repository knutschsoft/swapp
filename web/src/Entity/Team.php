<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Dto\Team\TeamChangeRequest;
use App\Dto\Team\TeamCreateRequest;
use App\Entity\Fields\AgeRangeField;
use App\Entity\Fields\UserGroupNamesField;
use App\Repository\DoctrineORMTeamRepository;
use App\Security\Voter\ClientVoter;
use App\Security\Voter\TeamVoter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Table(name: 'team')]
#[ORM\Entity(repositoryClass: DoctrineORMTeamRepository::class)]
#[ApiResource(
    collectionOperations: [
    "get",
    "team_change" => [
        "messenger" => "input",
        "input" => TeamChangeRequest::class,
        "output" => Team::class,
        "method" => "post",
        "status" => 200,
        "path" => "/teams/change",
        "security_post_denormalize" => 'is_granted("'.User::ROLE_ADMIN.'") && is_granted("'.ClientVoter::READ.'", object.team.getClient())',
    ],
    "team_create" => [
        "messenger" => "input",
        "input" => TeamCreateRequest::class,
        "output" => Team::class,
        "method" => "post",
        "status" => 200,
        "path" => "/teams/create",
        "security_post_denormalize" => 'is_granted("'.User::ROLE_ADMIN.'") && is_granted("'.ClientVoter::READ.'", object.client)',
    ],
    ],
    itemOperations: [
    'get' => [
        'security' => 'is_granted("'.TeamVoter::TEAM_READ.'", object)',
    ],
    ],
    normalizationContext: ["groups" => ["team:read"]]
)]
class Team
{
    use AgeRangeField;
    use UserGroupNamesField;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'teams')]
    private Collection $users;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue()]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'teams')]
    private Client $client;

    /** @var string[] */
    #[ORM\Column(type: 'array')]
    private array $locationNames;

    #[ORM\Column(type: 'boolean')]
    private bool $isWithContactsCount;

    #[ORM\Column(type: 'boolean')]
    private bool $isWithUserGroups;

    public function __construct()
    {
        $this->ageRanges = [];
        $this->userGroupNames = [];
        $this->locationNames = [];
        $this->users = new ArrayCollection();
    }

    #[Groups(['user:read', 'team:read'])]
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    #[Groups(['user:read', 'team:read'])]
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection<int, User>
     */
    #[Groups(['team:read'])]
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addTeam($this);
        }
    }

    public function removeUser(User $user): void
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeTeam($this);
        }
    }

    /** @param Collection<int, User> $users */
    public function setUsers(Collection $users): void
    {
        foreach ($this->users as $user) {
            $user->removeTeam($this);
        }
        $this->users = $users;
        foreach ($this->users as $user) {
            $user->addTeam($this);
        }
    }

    #[Groups(['team:read'])]
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
        return $this->name;
    }

    /**
     * @return string[]
     */
    #[Groups(['team:read'])]
    public function getLocationNames(): array
    {
        return $this->locationNames;
    }

    /**
     * @param string[] $locationNames
     */
    public function setLocationNames(array $locationNames): void
    {
        $locationNames = \array_map('trim', $locationNames);
        \natcasesort($locationNames);
        $this->locationNames = \array_values(\array_unique($locationNames));
    }

    #[Groups(['team:read'])]
    #[SerializedName('isWithContactsCount')]
    public function isWithContactsCount(): bool
    {
        return $this->isWithContactsCount;
    }

    public function setIsWithContactsCount(bool $isWithContactsCount): void
    {
        $this->isWithContactsCount = $isWithContactsCount;
    }

    #[Groups(['team:read'])]
    #[SerializedName('isWithUserGroups')]
    public function isWithUserGroups(): bool
    {
        return $this->isWithUserGroups;
    }

    public function setIsWithUserGroups(bool $isWithUserGroups): void
    {
        $this->isWithUserGroups = $isWithUserGroups;
    }
}
