<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Dto\Team\TeamChangeRequest;
use App\Dto\Team\TeamCreateRequest;
use App\Entity\Fields\AgeRangeField;
use App\Entity\Fields\UserGroupNamesField;
use App\Repository\DoctrineORMTeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ApiResource(
    operations: [
        new Get(security: 'is_granted("TEAM_READ", object)'),
        new GetCollection(),
        new Post(
            uriTemplate: '/teams/change',
            status: 200,
            securityPostDenormalize: 'is_granted("ROLE_ADMIN") && is_granted("CLIENT_READ", object.team.getClient())',
            input: TeamChangeRequest::class,
            output: Team::class,
            messenger: 'input'
        ),
        new Post(
            uriTemplate: '/teams/create',
            status: 200,
            securityPostDenormalize: 'is_granted("ROLE_ADMIN") && is_granted("CLIENT_READ", object.client)',
            input: TeamCreateRequest::class,
            output: Team::class,
            messenger: 'input'
        ),
    ],
    normalizationContext: ['groups' => ['team:read']]
)]
#[ORM\Table(name: 'team')]
#[ORM\Entity(repositoryClass: DoctrineORMTeamRepository::class)]
class Team
{
    use AgeRangeField;
    use UserGroupNamesField;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'teams')]
    private Collection $users;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'teams')]
    private Client $client;

    /** @var string[] */
    #[ORM\Column(type: 'array')]
    private array $guestNames;

    /** @var string[] */
    #[ORM\Column(type: 'array')]
    private array $locationNames;

    /** @var string[] */
    #[ORM\Column(type: Types::JSON)]
    private array $walkNames;

    /** @var string[] */
    #[ORM\Column(type: Types::JSON)]
    private array $conceptOfDaySuggestions;

    #[ORM\Column(type: 'boolean')]
    private bool $isWithAgeRanges;

    #[ORM\Column(type: 'boolean')]
    private bool $isWithPeopleCount;

    #[ORM\Column(type: 'boolean')]
    private bool $isWithGuests;

    #[ORM\Column(type: 'boolean')]
    private bool $isWithContactsCount;

    #[ORM\Column(type: 'boolean')]
    private bool $isWithUserGroups;

    public function __construct()
    {
        $this->ageRanges = [];
        $this->userGroupNames = [];
        $this->guestNames = [];
        $this->isWithGuests = false;
        $this->isWithPeopleCount = false;
        $this->isWithAgeRanges = true;
        $this->locationNames = [];
        $this->walkNames = [];
        $this->conceptOfDaySuggestions = [];
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

    /**
     * @return string[]
     */
    #[Groups(['team:read'])]
    public function getWalkNames(): array
    {
        return $this->walkNames;
    }

    /**
     * @param string[] $walkNames
     */
    public function setWalkNames(array $walkNames): void
    {
        $walkNames = \array_map('trim', $walkNames);
        \natcasesort($walkNames);
        $this->walkNames = \array_values(\array_unique($walkNames));
    }

    /**
     * @return string[]
     */
    #[Groups(['team:read'])]
    public function getConceptOfDaySuggestions(): array
    {
        return $this->conceptOfDaySuggestions;
    }

    /**
     * @param string[] $conceptOfDaySuggestions
     */
    public function setConceptOfDaySuggestions(array $conceptOfDaySuggestions): void
    {
        $conceptOfDaySuggestions = \array_map('trim', $conceptOfDaySuggestions);
        \natcasesort($conceptOfDaySuggestions);
        $this->conceptOfDaySuggestions = \array_values(\array_unique($conceptOfDaySuggestions));
    }

    /**
     * @return string[]
     */
    #[Groups(['team:read'])]
    public function getGuestNames(): array
    {
        return $this->guestNames;
    }

    /**
     * @param string[] $guestNames
     */
    public function setGuestNames(array $guestNames): void
    {
        $guestNames = \array_map('trim', $guestNames);
        \natcasesort($guestNames);
        $this->guestNames = \array_values(\array_unique($guestNames));
    }

    #[Groups(['team:read'])]
    #[SerializedName('isWithGuests')]
    public function isWithGuests(): bool
    {
        return $this->isWithGuests;
    }

    public function setIsWithGuests(bool $isWithGuests): void
    {
        $this->isWithGuests = $isWithGuests;
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

    #[Groups(['team:read'])]
    #[SerializedName('isWithAgeRanges')]
    public function isWithAgeRanges(): bool
    {
        return $this->isWithAgeRanges;
    }

    public function setIsWithAgeRanges(bool $isWithAgeRanges): void
    {
        $this->isWithAgeRanges = $isWithAgeRanges;
    }

    #[Groups(['team:read'])]
    #[SerializedName('isWithPeopleCount')]
    public function isWithPeopleCount(): bool
    {
        return $this->isWithPeopleCount;
    }

    public function setIsWithPeopleCount(bool $isWithPeopleCount): void
    {
        $this->isWithPeopleCount = $isWithPeopleCount;
    }
}
