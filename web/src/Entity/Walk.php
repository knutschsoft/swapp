<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\DataProvider\GuestNameCollectionProvider;
use App\Dto\GuestName;
use App\Dto\TeamName;
use App\Dto\Walk\WalkChangeRequest;
use App\Dto\Walk\WalkChangeStartTimeRequest;
use App\Dto\Walk\WalkChangeUnfinishedRequest;
use App\Dto\Walk\WalkCreateRequest;
use App\Dto\Walk\WalkEpilogueRequest;
use App\Dto\Walk\WalkRemoveRequest;
use App\Dto\WalkConceptOfDay;
use App\Dto\WalkName;
use App\Entity\Fields\AgeRangeField;
use App\Entity\Fields\ConsumableNamesField;
use App\Entity\Fields\CounselingNamesField;
use App\Entity\Fields\MedicalNamesField;
use App\Entity\Fields\UserGroupNamesField;
use App\Repository\DoctrineORMWalkRepository;
use App\Security\Voter\TeamVoter;
use App\Security\Voter\WalkVoter;
use App\Value\AgeGroup;
use App\Value\AgeRange;
use App\Value\Consumable;
use App\Value\Counseling;
use App\Value\Medical;
use App\Value\UserGroup;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ApiResource(
    operations: [
        new Get(
            requirements: ['id' => '\d+'],
        ),
        new GetCollection(),
        new GetCollection(
            uriTemplate: '/walks/team_names',
            output: TeamName::class,
            forceEager: false,
        ),
        new GetCollection(
            uriTemplate: '/walks/walk_names',
            output: WalkName::class,
            forceEager: false,
        ),
        new GetCollection(
            uriTemplate: '/walks/concepts_of_day',
            output: WalkConceptOfDay::class,
            forceEager: false,
        ),
        new GetCollection(
            uriTemplate: '/walks/guest_names',
            output: GuestName::class,
            forceEager: false,
            provider: GuestNameCollectionProvider::class,
        ),
        new Post(
            uriTemplate: '/walks/change-unfinished',
            status: 200,
            securityPostDenormalize: 'is_granted("' . WalkVoter::EDIT . '", object.walk)',
            input: WalkChangeUnfinishedRequest::class,
            output: Walk::class,
            messenger: 'input'
        ),
        new Post(
            uriTemplate: '/walks/change',
            status: 200,
            securityPostDenormalize: 'is_granted("' . WalkVoter::EDIT . '", object.walk)',
            input: WalkChangeRequest::class,
            output: Walk::class,
            messenger: 'input'
        ),
        new Post(
            uriTemplate: '/walks/change-start-time',
            status: 200,
            securityPostDenormalize: 'is_granted("' . WalkVoter::EDIT_START_TIME . '", object.walk)',
            input: WalkChangeStartTimeRequest::class,
            output: Walk::class,
            messenger: 'input'
        ),
        new Post(
            uriTemplate: '/walks/epilogue',
            status: 200,
            securityPostDenormalize: 'is_granted("' . WalkVoter::READ . '", object.walk)',
            input: WalkEpilogueRequest::class,
            output: Walk::class,
            messenger: 'input'
        ),
        new Post(
            uriTemplate: '/walks/create',
            status: 200,
            securityPostDenormalize: 'is_granted("' . TeamVoter::TEAM_READ . '", object.team) and user.hasTeam(object.team)',
            input: WalkCreateRequest::class,
            output: Walk::class,
            messenger: 'input'
        ),
        new Post(
            uriTemplate: '/walks/remove',
            status: 200,
            securityPostDenormalize: 'is_granted("' . WalkVoter::REMOVE . '", object.walk)',
            input: WalkRemoveRequest::class,
            messenger: 'input'
        ),
    ],
    normalizationContext: ['groups' => ['walk:read']],
    order: ['teamName' => 'ASC'],
    paginationItemsPerPage: 5
)]
#[ORM\Table(name: 'walk')]
#[ORM\Index(name: "idx_walk_name", columns: ["name"])]
#[ORM\Index(name: "idx_walk_teamName", columns: ["teamName"])]
#[ORM\Index(name: "idx_walk_name_teamName", columns: ["name", "teamName"])]
#[ORM\Entity(repositoryClass: DoctrineORMWalkRepository::class)]
#[ApiFilter(filterClass: OrderFilter::class, properties: ['id', 'name', 'rating', 'teamName', 'startTime', 'endTime', 'isResubmission'])]
#[ApiFilter(filterClass: BooleanFilter::class, properties: ['isResubmission', 'isUnfinished'])]
#[ApiFilter(filterClass: DateFilter::class, properties: ['startTime', 'endTime'])]
#[ApiFilter(filterClass: SearchFilter::class, properties: [
    'name' => SearchFilterInterface::STRATEGY_PARTIAL,
    'teamName' => SearchFilterInterface::STRATEGY_PARTIAL,
    'guestNames' => SearchFilterInterface::STRATEGY_IPARTIAL,
])]
class Walk implements \Stringable
{
    use AgeRangeField;
    use ConsumableNamesField;
    use CounselingNamesField;
    use MedicalNamesField;
    use UserGroupNamesField;

    #[ApiProperty(identifier: true)]
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(length: 50)]
    private string $name;

    /** @var Collection<int, WayPoint> */
    #[ORM\OneToMany(targetEntity: WayPoint::class, mappedBy: 'walk')]
    private Collection $wayPoints;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $startTime;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(type: Types::STRING, length: 4096)]
    private string $walkReflection = '';

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'walks', cascade: ['persist'], orphanRemoval: false)]
    #[ORM\OrderBy(value: ['username' => 'ASC'])]
    private Collection $walkTeamMembers;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'createdWalks')]
    private ?User $walkCreator = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $rating;

    #[ORM\Column(type: Types::STRING, length: 4096)]
    private string $systemicQuestion = '';

    #[ORM\Column(type: Types::STRING, length: 4096)]
    private string $systemicAnswer = '';

    #[ORM\Column(type: Types::TEXT, length: 4096)]
    private string $insights = '';

    #[ORM\Column(type: Types::TEXT, length: 4096)]
    private string $commitments = '';

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isResubmission;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $weather;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $holidays = null;

    /** @var string[] */
    #[ORM\Column(type: Types::JSON)]
    private array $conceptOfDay = [];

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $teamName;

    #[ORM\Column(name: 'deletedAt', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $deletedAt = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'walks')]
    #[ORM\OrderBy(value: ['order' => 'asc'])]
    #[ORM\JoinColumn(nullable: false)]
    private Client $client;

    /** @var string[] */
    #[ORM\Column(type: Types::JSON)]
    private array $guestNames = [];

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithGuests;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithSystemicQuestion = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithWeather = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithHolidays = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithAgeRanges;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isWithPeopleCount;

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

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isUnfinished = true;

    public function __construct()
    {
        $this->ageRanges = [];
        $this->consumableNames = [];
        $this->counselingNames = [];
        $this->medicalNames = [];
        $this->userGroupNames = [];
        $this->walkTeamMembers = new ArrayCollection();
        $this->wayPoints = new ArrayCollection();
    }

    public static function fromWalkCreateRequest(WalkCreateRequest $request): self
    {
        $instance = new self();
        $team = $request->team;

        $instance->setWalkTeamMembers(new ArrayCollection($request->walkTeamMembers));
        $instance->setTeamName($team->getName());
        $instance->setIsWithGuests($team->isWithGuests());
        if ($team->isWithGuests()) {
            $instance->setGuestNames($request->guestNames);
        }
        $instance->setIsWithPeopleCount($team->isWithPeopleCount());
        $instance->setIsWithAgeRanges($team->isWithAgeRanges());
        if ($instance->isWithAgeRanges()) {
            $instance->setAgeRanges($team->getAgeRanges());
        }
        $instance->setIsWithContactsCount($team->isWithContactsCount());
        $instance->setIsWithUserGroups($team->isWithUserGroups());
        $instance->setIsWithConsumables($team->isWithConsumables());
        $instance->setIsWithCounselings($team->isWithCounselings());
        $instance->setIsWithMedicals($team->isWithMedicals());
        $instance->setUserGroupNames($team->getUserGroupNames());
        $instance->setConsumableNames($team->getConsumableNames());
        $instance->setCounselingNames($team->getCounselingNames());
        $instance->setMedicalNames($team->getMedicalNames());
        $instance->updateClient($team->getClient());
        $instance->setName($request->name);
        $instance->setStartTime($request->startTime);
        $instance->setRating(1);
        $instance->setIsWithWeather($team->isWithWeather());
        $instance->setIsWithHolidays($team->isWithHolidays());
        $instance->setIsWithSystemicQuestion($team->isWithSystemicQuestion());
        if ($instance->isWithSystemicQuestion()) {
            $instance->setSystemicAnswer('');
        }
        $instance->setWalkReflection('');
        $instance->setWeather($request->weather);
        $instance->setIsResubmission(false);
        if ($instance->isWithHolidays()) {
            $instance->setHolidays($request->holidays);
        }
        $instance->setCommitments('');
        $instance->setInsights('');
        $instance->setConceptOfDay($request->conceptOfDay);

        return $instance;
    }

    /** @return string[] */
    #[Groups(['walk:read'])]
    public function getConceptOfDay(): array
    {
        return $this->conceptOfDay;
    }

    /** @param string[] $conceptOfDay */
    public function setConceptOfDay(array $conceptOfDay): void
    {
        $this->conceptOfDay = $conceptOfDay;
    }

    #[Groups(['walk:read'])]
    public function getInsights(): string
    {
        return $this->insights;
    }

    public function setInsights(string $insights): void
    {
        $this->insights = $insights;
    }

    #[Groups(['walk:read'])]
    public function getCommitments(): string
    {
        return $this->commitments;
    }

    public function setCommitments(string $commitments): void
    {
        $this->commitments = $commitments;
    }

    #[Groups(['walk:read'])]
    public function getIsResubmission(): bool
    {
        return $this->isResubmission;
    }

    public function setIsResubmission(bool $isResubmission): void
    {
        $this->isResubmission = $isResubmission;
    }

    #[Groups(['walk:read'])]
    public function getSystemicQuestion(): string
    {
        return $this->systemicQuestion;
    }

    public function setSystemicQuestion(string $systemicQuestion): void
    {
        $this->systemicQuestion = $systemicQuestion;
    }

    #[Groups(['walk:read'])]
    #[SerializedName('isWithSystemicQuestion')]
    public function isWithSystemicQuestion(): bool
    {
        return $this->isWithSystemicQuestion;
    }

    public function setIsWithSystemicQuestion(bool $isWithSystemicQuestion): void
    {
        $this->isWithSystemicQuestion = $isWithSystemicQuestion;
    }

    #[Groups(['walk:read'])]
    #[SerializedName('isWithWeather')]
    public function isWithWeather(): bool
    {
        return $this->isWithWeather;
    }

    public function setIsWithWeather(bool $isWithWeather): void
    {
        $this->isWithWeather = $isWithWeather;
    }

    #[Groups(['walk:read'])]
    #[SerializedName('isWithHolidays')]
    public function isWithHolidays(): bool
    {
        return $this->isWithHolidays;
    }

    public function setIsWithHolidays(bool $isWithHolidays): void
    {
        $this->isWithHolidays = $isWithHolidays;
    }

    #[Groups(['walk:read'])]
    public function getWeather(): string
    {
        return $this->weather;
    }

    public function setWeather(string $weather): void
    {
        $this->weather = $weather;
    }

    #[Groups(['walk:read'])]
    public function getHolidays(): ?bool
    {
        return $this->holidays;
    }

    public function setHolidays(?bool $holidays): void
    {
        $this->holidays = $holidays;
    }

    #[Groups(['walk:read'])]
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = \strip_tags($name);
    }

    #[Groups(['walk:read'])]
    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): void
    {
        $this->endTime = $endTime;
    }

    #[Groups(['walk:read'])]
    #[SerializedName('walkId')]
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    #[Groups(['walk:read'])]
    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    #[Groups(['walk:read'])]
    public function getStartTime(): \DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): void
    {
        $this->startTime = Carbon::parse($startTime)->startOfMinute();
    }

    #[Groups(['walk:read'])]
    public function getSystemicAnswer(): string
    {
        return $this->systemicAnswer;
    }

    public function setSystemicAnswer(string $systemicAnswer): void
    {
        $this->systemicAnswer = $systemicAnswer;
    }

    #[Groups(['walk:read'])]
    public function getWalkReflection(): string
    {
        return $this->walkReflection;
    }

    public function setWalkReflection(string $walkReflection): void
    {
        $this->walkReflection = $walkReflection;
    }

    /**
     * @return Collection<int,User>
     */
    #[Groups(['walk:read'])]
    public function getWalkTeamMembers(): Collection
    {
        return $this->walkTeamMembers;
    }

    /**
     * @param Collection<int,User> $walkTeamMembers
     */
    public function setWalkTeamMembers(Collection $walkTeamMembers): void
    {
        /** @var User $walkTeamMember */
        foreach ($this->walkTeamMembers as $walkTeamMember) {
            $walkTeamMember->removeWalk($this);
        }
        $this->walkTeamMembers = $walkTeamMembers;
        /** @var User $walkTeamMember */
        foreach ($walkTeamMembers as $walkTeamMember) {
            $walkTeamMember->addWalk($this);
        }
    }

    #[Groups(['walk:read'])]
    public function getWalkCreator(): ?User
    {
        return $this->walkCreator;
    }

    public function setWalkCreator(User $walkCreator): void
    {
        $this->walkCreator = $walkCreator;
    }

    /**
     * @return Collection<int, WayPoint>
     */
    #[Groups(['walk:read'])]
    public function getWayPoints(): Collection
    {
        return $this->wayPoints;
    }

    /**
     * @param Collection<int, WayPoint> $wayPoints
     */
    public function setWayPoints(Collection $wayPoints): void
    {
        $this->wayPoints = $wayPoints;
    }

    #[Groups(['walk:read'])]
    public function getTeamName(): string
    {
        return $this->teamName;
    }

    public function setTeamName(string $teamName): void
    {
        $this->teamName = $teamName;
    }

    #[Groups(['walk:read'])]
    public function getPeopleCount(): int
    {
        $count = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $count += $wayPoint->getPeopleCount();
        }

        return $count;
    }

    #[Groups(['walk:read'])]
    public function getFemalesCount(): int
    {
        $count = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $count += $wayPoint->getFemalesCount();
        }

        return $count;
    }

    #[Groups(['walk:read'])]
    public function getMalesCount(): int
    {
        $count = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $count += $wayPoint->getMalesCount();
        }

        return $count;
    }

    #[Groups(['walk:read'])]
    public function getQueerCount(): int
    {
        $count = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $count += $wayPoint->getQueerCount();
        }

        return $count;
    }

    public function getFemalesCountForAgeRange(AgeRange $ageRange): int
    {
        $count = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $count += $wayPoint->getFemalesCountForAgeRange($ageRange);
        }

        return $count;
    }

    public function getMalesCountForAgeRange(AgeRange $ageRange): int
    {
        $count = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $count += $wayPoint->getMalesCountForAgeRange($ageRange);
        }

        return $count;
    }

    public function getQueerCountForAgeRange(AgeRange $ageRange): int
    {
        $count = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $count += $wayPoint->getQueerCountForAgeRange($ageRange);
        }

        return $count;
    }

    #[Groups(['walk:read'])]
    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function addWayPoint(WayPoint $wayPoint): self
    {
        $this->wayPoints[] = $wayPoint;

        return $this;
    }

    public function removeWayPoint(WayPoint $wayPoint): void
    {
        $this->wayPoints->removeElement($wayPoint);
    }

    public function addWalkTeamMember(User $walkTeamMember): self
    {
        $this->walkTeamMembers[] = $walkTeamMember;

        return $this;
    }

    public function removeWalkTeamMember(User $walkTeamMember): void
    {
        $this->walkTeamMembers->removeElement($walkTeamMember);
    }

    #[Groups(['walk:read'])]
    public function getIsUnfinished(): bool
    {
        return $this->isUnfinished;
    }

    public function setIsUnfinished(bool $isUnfinished): void
    {
        $this->isUnfinished = $isUnfinished;
    }

    #[Groups(['walk:read'])]
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
            '%s',
            $this->getName()
        );
    }

    #[Groups(['walk:read'])]
    #[SerializedName('isWithGuests')]
    public function isWithGuests(): bool
    {
        return $this->isWithGuests;
    }

    public function setIsWithGuests(bool $isWithGuests): void
    {
        $this->isWithGuests = $isWithGuests;
    }

    /**
     * @return string[]
     */
    #[Groups(['walk:read'])]
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

    #[Groups(['walk:read'])]
    #[SerializedName('isWithAgeRanges')]
    public function isWithAgeRanges(): bool
    {
        return $this->isWithAgeRanges;
    }

    public function setIsWithAgeRanges(bool $isWithAgeRanges): void
    {
        $this->isWithAgeRanges = $isWithAgeRanges;
    }

    #[Groups(['walk:read'])]
    #[SerializedName('isWithPeopleCount')]
    public function isWithPeopleCount(): bool
    {
        return $this->isWithPeopleCount;
    }

    public function setIsWithPeopleCount(bool $isWithPeopleCount): void
    {
        $this->isWithPeopleCount = $isWithPeopleCount;
    }

    #[Groups(['walk:read'])]
    #[SerializedName('isWithContactsCount')]
    public function isWithContactsCount(): bool
    {
        return $this->isWithContactsCount;
    }

    public function setIsWithContactsCount(bool $isWithContactsCount): void
    {
        $this->isWithContactsCount = $isWithContactsCount;
    }

    #[Groups(['walk:read'])]
    #[SerializedName('isWithUserGroups')]
    public function isWithUserGroups(): bool
    {
        return $this->isWithUserGroups;
    }

    public function setIsWithUserGroups(bool $isWithUserGroups): void
    {
        $this->isWithUserGroups = $isWithUserGroups;
    }

    #[Groups(['walk:read'])]
    #[SerializedName('isWithConsumables')]
    public function isWithConsumables(): bool
    {
        return $this->isWithConsumables;
    }

    public function setIsWithConsumables(bool $isWithConsumables): void
    {
        $this->isWithConsumables = $isWithConsumables;
    }

    #[Groups(['walk:read'])]
    #[SerializedName('isWithCounselings')]
    public function isWithCounselings(): bool
    {
        return $this->isWithCounselings;
    }

    public function setIsWithCounselings(bool $isWithCounselings): void
    {
        $this->isWithCounselings = $isWithCounselings;
    }

    #[Groups(['walk:read'])]
    #[SerializedName('isWithMedicals')]
    public function isWithMedicals(): bool
    {
        return $this->isWithMedicals;
    }

    public function setIsWithMedicals(bool $isWithMedicals): void
    {
        $this->isWithMedicals = $isWithMedicals;
    }

    public function getSumOfContactsCount(): ?int
    {
        if (!$this->isWithContactsCount) {
            return null;
        }
        $sumOfContactsCount = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $sumOfContactsCount += (int) $wayPoint->getContactsCount();
        }

        return $sumOfContactsCount;
    }

    /**
     * @return AgeGroup[]
     */
    public function getAgeGroups(): array
    {
        $ageGroups = [];
        foreach ($this->getWayPoints() as $wayPoint) {
            $ageGroups = \array_merge($ageGroups, $wayPoint->getAgeGroups());
        }

        return $ageGroups;
    }

    /**
     * @return UserGroup[]
     */
    public function getUserGroups(): array
    {
        $userGroups = [];
        foreach ($this->getWayPoints() as $wayPoint) {
            $userGroups = \array_merge($userGroups, $wayPoint->getUserGroups());
        }

        return $userGroups;
    }

    /**
     * @return Consumable[]
     */
    public function getConsumables(): array
    {
        $consumables = [];
        foreach ($this->getWayPoints() as $wayPoint) {
            $consumables = \array_merge($consumables, $wayPoint->getConsumables());
        }

        return $consumables;
    }

    /**
     * @return Counseling[]
     */
    public function getCounselings(): array
    {
        $counselings = [];
        foreach ($this->getWayPoints() as $wayPoint) {
            $counselings = \array_merge($counselings, $wayPoint->getCounselings());
        }

        return $counselings;
    }

    /**
     * @return Medical[]
     */
    public function getMedicals(): array
    {
        $medicals = [];
        foreach ($this->getWayPoints() as $wayPoint) {
            $medicals = \array_merge($medicals, $wayPoint->getMedicals());
        }

        return $medicals;
    }
}
