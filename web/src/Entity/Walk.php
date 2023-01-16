<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Dto\TeamName;
use App\Dto\Walk\WalkChangeRequest;
use App\Dto\Walk\WalkCreateRequest;
use App\Dto\Walk\WalkEpilogueRequest;
use App\Dto\WalkExportRequest;
use App\Entity\Fields\AgeRangeField;
use App\Entity\Fields\UserGroupNamesField;
use App\Repository\DoctrineORMWalkRepository;
use App\Value\AgeRange;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;
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
        new Post(
            uriTemplate: '/walks/export',
            status: 200,
            openapiContext: ['summary' => 'Exports all walks for given date range.'],
            securityPostDenormalize: 'is_granted("CLIENT_READ", object.client)',
            input: WalkExportRequest::class,
            output: Response::class,
            messenger: 'input'
        ),
        new Post(
            uriTemplate: '/walks/change',
            status: 200,
            securityPostDenormalize: 'is_granted(\'WALK_EDIT\', object.walk)',
            input: WalkChangeRequest::class,
            output: Walk::class,
            messenger: 'input'
        ),
        new Post(
            uriTemplate: '/walks/epilogue',
            status: 200,
            securityPostDenormalize: 'is_granted(\'WALK_READ\', object.walk)',
            input: WalkEpilogueRequest::class,
            output: Walk::class,
            messenger: 'input'
        ),
        new Post(
            uriTemplate: '/walks/create',
            status: 200,
            securityPostDenormalize: 'is_granted(\'TEAM_READ\', object.team) and user.hasTeam(object.team)',
            input: WalkCreateRequest::class,
            output: Walk::class,
            messenger: 'input'
        ),
    ],
    normalizationContext: ['groups' => ['walk:read']],
    order: ['teamName' => 'ASC'],
    paginationItemsPerPage: 5
)]
#[ORM\Table(name: 'walk')]
#[ORM\Entity(repositoryClass: DoctrineORMWalkRepository::class)]
#[ApiFilter(filterClass: OrderFilter::class, properties: ['name', 'rating', 'teamName', 'startTime', 'endTime', 'isResubmission'])]
#[ApiFilter(filterClass: BooleanFilter::class, properties: ['isResubmission', 'isUnfinished'])]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'teamName' => 'partial'])]
class Walk
{
    use AgeRangeField;
    use UserGroupNamesField;

    #[ApiProperty(identifier: true)]
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'string', length: 1024)]
    private string $name;

    /** @var Collection<int, WayPoint> **/
    #[ORM\OneToMany(mappedBy: 'walk', targetEntity: WayPoint::class)]
    private Collection $wayPoints;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $startTime;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $endTime;

    #[ORM\Column(type: 'string', length: 4096)]
    private string $walkReflection;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'walks', cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(value: ['username' => 'ASC'])]
    private Collection $walkTeamMembers;

    /** @var Collection<int, Tag> */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'walks')]
    private Collection $walkTags;

    #[ORM\Column(type: 'smallint')]
    private int $rating;

    #[ORM\Column(type: 'string', length: 4096)]
    private string $systemicQuestion;

    #[ORM\Column(type: 'string', length: 4096)]
    private string $systemicAnswer;

    #[ORM\Column(type: 'text', length: 4096)]
    private string $insights;

    #[ORM\Column(type: 'text', length: 4096)]
    private string $commitments;

    #[ORM\Column(type: 'boolean')]
    private bool $isResubmission;

    #[ORM\Column(type: 'string', length: 255)]
    private string $weather;

    #[ORM\Column(type: 'boolean')]
    private bool $holidays;

    #[ORM\Column(type: 'string', length: 4096)]
    private string $conceptOfDay;

    #[ORM\Column(type: 'string', length: 255)]
    private string $teamName;

    #[ORM\Column(name: 'deletedAt', type: 'datetime', nullable: true)]
    private ?\DateTime $deletedAt = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'walks')]
    #[ORM\OrderBy(value: ['order' => 'asc'])]
    private Client $client;

    /** @var string[] */
    #[ORM\Column(type: 'array')]
    private array $guestNames;

    #[ORM\Column(type: 'boolean')]
    private bool $isWithGuests;

    #[ORM\Column(type: 'boolean')]
    private bool $isWithAgeRanges;

    #[ORM\Column(type: 'boolean')]
    private bool $isWithPeopleCount;

    #[ORM\Column(type: 'boolean')]
    private bool $isWithContactsCount;

    #[ORM\Column(type: 'boolean')]
    private bool $isWithUserGroups;

    #[ORM\Column(type: 'boolean')]
    private bool $isUnfinished;

    public function __construct()
    {
        $this->ageRanges = [];
        $this->userGroupNames = [];
        $this->guestNames = [];
        $this->walkTags = new ArrayCollection();
        $this->walkTeamMembers = new ArrayCollection();
        $this->wayPoints = new ArrayCollection();
        $this->holidays = false;
        $this->conceptOfDay = '';
        $this->commitments = '';
        $this->insights = '';
        $this->systemicAnswer = '';
        $this->walkReflection = '';
        $this->endTime = null;
        $this->isUnfinished = true;
    }

    public static function fromWalkCreateRequest(WalkCreateRequest $request, SystemicQuestion $systemicQuestion): self
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
        $instance->setUserGroupNames($team->getUserGroupNames());
        $instance->updateClient($team->getClient());
        $instance->setName($request->name);
        $instance->setStartTime($request->startTime);
        $instance->setRating(1);
        $instance->setSystemicAnswer('');
        $instance->setSystemicQuestion($systemicQuestion->getQuestion());
        $instance->setWalkReflection('');
        $instance->setWeather($request->weather);
        $instance->setIsResubmission(false);
        $instance->setHolidays($request->holidays);
        $instance->setCommitments('');
        $instance->setInsights('');
        $instance->setConceptOfDay($request->conceptOfDay);

        return $instance;
    }

    #[Groups(['walk:read'])]
    public function getConceptOfDay(): string
    {
        return $this->conceptOfDay;
    }

    public function setConceptOfDay(string $conceptOfDay): void
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
    public function getWeather(): string
    {
        return $this->weather;
    }

    public function setWeather(string $weather): void
    {
        $this->weather = $weather;
    }

    #[Groups(['walk:read'])]
    public function getHolidays(): bool
    {
        return $this->holidays;
    }

    public function setHolidays(bool $holidays): void
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
        $this->startTime = $startTime;
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

    public function addWalkTag(Tag $tag): void
    {
        $tag->addWalk($this);
        $this->walkTags->add($tag);
    }

    public function removeWalkTag(Tag $tag): void
    {
        $tag->removeWalk($this);
        $this->walkTags->removeElement($tag);
    }

    /**
     * @return Collection<int,Tag>
     */
    #[Groups(['walk:read'])]
    public function getWalkTags(): Collection
    {
        return $this->walkTags;
    }

    /**
     * @param Collection<int,Tag> $walkTags
     */
    public function setWalkTags(Collection $walkTags): void
    {
        $this->walkTags = $walkTags;
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
}
