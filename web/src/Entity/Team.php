<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Dto\Team\TeamChangeRequest;
use App\Dto\Team\TeamCreateRequest;
use App\Entity\Fields\AgeRangeField;
use App\Entity\Fields\ConsumableNamesField;
use App\Entity\Fields\CounselingNamesField;
use App\Entity\Fields\MedicalNamesField;
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
#[ApiFilter(filterClass: OrderFilter::class, properties: ['name', 'client.name'])]
#[ORM\Table(name: 'team')]
#[ORM\Entity(repositoryClass: DoctrineORMTeamRepository::class)]
class Team implements \Stringable
{
    use AgeRangeField;
    use ConsumableNamesField;
    use CounselingNamesField;
    use MedicalNamesField;
    use UserGroupNamesField;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'teams')]
    private Collection $users;

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(length: 50)]
    private string $name = '';

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'teams')]
    #[ORM\JoinColumn(nullable: false)]
    private Client $client;

    /** @var string[] */
    #[ORM\Column(type: Types::JSON)]
    private array $guestNames = [];

    /** @var string[] */
    #[ORM\Column(type: Types::JSON)]
    private array $locationNames = [];

    /** @var string[] */
    #[ORM\Column(type: Types::JSON)]
    private array $walkNames = [];

    /** @var string[] */
    #[ORM\Column(type: Types::JSON)]
    private array $conceptOfDaySuggestions = [];

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithAgeRanges = true;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithPeopleCount = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithGuests = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithSystemicQuestion = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithWeather = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithHolidays = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithContactsCount;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithUserGroups;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithConsumables;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithCounselings;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithMedicals;

    #[ORM\Column(length: 15)]
    private string $initialMembersConfig;

    public function __construct()
    {
        $this->ageRanges = [];
        $this->userGroupNames = [];
        $this->consumableNames = [];
        $this->counselingNames = [];
        $this->medicalNames = [];
        $this->users = new ArrayCollection();
    }

    #[Groups(['user:read', 'team:read'])]
    #[SerializedName('teamId')]
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
    #[SerializedName('isWithSystemicQuestion')]
    public function isWithSystemicQuestion(): bool
    {
        return $this->isWithSystemicQuestion;
    }

    public function setIsWithSystemicQuestion(bool $isWithSystemicQuestion): void
    {
        $this->isWithSystemicQuestion = $isWithSystemicQuestion;
    }

    #[Groups(['team:read'])]
    #[SerializedName('isWithWeather')]
    public function isWithWeather(): bool
    {
        return $this->isWithWeather;
    }

    public function setIsWithWeather(bool $isWithWeather): void
    {
        $this->isWithWeather = $isWithWeather;
    }

    #[Groups(['team:read'])]
    #[SerializedName('isWithHolidays')]
    public function isWithHolidays(): bool
    {
        return $this->isWithHolidays;
    }

    public function setIsWithHolidays(bool $isWithHolidays): void
    {
        $this->isWithHolidays = $isWithHolidays;
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
    #[SerializedName('isWithConsumables')]
    public function isWithConsumables(): bool
    {
        return $this->isWithConsumables;
    }

    public function setIsWithConsumables(bool $isWithConsumables): void
    {
        $this->isWithConsumables = $isWithConsumables;
    }

    #[Groups(['team:read'])]
    #[SerializedName('isWithCounselings')]
    public function isWithCounselings(): bool
    {
        return $this->isWithCounselings;
    }

    public function setIsWithCounselings(bool $isWithCounselings): void
    {
        $this->isWithCounselings = $isWithCounselings;
    }

    #[Groups(['team:read'])]
    #[SerializedName('isWithMedicals')]
    public function isWithMedicals(): bool
    {
        return $this->isWithMedicals;
    }

    public function setIsWithMedicals(bool $isWithMedicals): void
    {
        $this->isWithMedicals = $isWithMedicals;
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

    #[Groups(['team:read'])]
    public function getInitialMembersConfig(): string
    {
        return $this->initialMembersConfig;
    }

    public function setInitialMembersConfig(string $initialMembersConfig): void
    {
        $this->initialMembersConfig = $initialMembersConfig;
    }

    /** @return string[] */
    public static function getInitialMembersConfigChoices(): array
    {
        return ['mitglieder', 'rundenersteller'];
    }
}
