<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Dto\TeamName;
use App\Dto\Walk\WalkChangeRequest;
use App\Dto\Walk\WalkCreateRequest;
use App\Dto\Walk\WalkEpilogueRequest;
use App\Dto\WalkExportRequest;
use App\Entity\Fields\AgeRangeField;
use App\Repository\DoctrineORMWalkRepository;
use App\Security\Voter\ClientVoter;
use App\Security\Voter\TeamVoter;
use App\Security\Voter\WalkVoter;
use App\Value\AgeRange;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Table(name: 'walk')]
#[ORM\Entity(repositoryClass: DoctrineORMWalkRepository::class)]
#[ApiFilter(OrderFilter::class, properties: ["name", "rating", "teamName", "startTime", "endTime", "isResubmission"])]
#[ApiFilter(BooleanFilter::class, properties: ["isResubmission"])]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial', 'teamName' => 'partial'])]
#[ApiResource(
    collectionOperations: [
        "get",
        "get_team_names" => [
            "method" => "get",
            "path" => "/walks/team_names",
            "output" => TeamName::class,
            "pagination_enabled" => false,
            "normalization_context" => ["groups" => ["teamName:read"]],
        ],
        "walk_export" => [
            "messenger" => "input",
            "openapi_context" => [
                "summary" => "Exports all walks for given date range.",
            ],
            "input" => WalkExportRequest::class,
            "output" => Response::class,
            "status" => 200,
            "method" => "post",
            "path" => "/walks/export",
            "security_post_denormalize" => 'is_granted("'.ClientVoter::READ.'", object.client)',
        ],
        "walk_change" => [
            "messenger" => "input",
            "input" => WalkChangeRequest::class,
            "output" => Walk::class,
            "method" => "post",
            "status" => 200,
            "path" => "/walks/change",
            "security_post_denormalize" => "is_granted('".WalkVoter::EDIT."', object.walk)",
        ],
        "walk_epilogue" => [
            "messenger" => "input",
            "input" => WalkEpilogueRequest::class,
            "output" => Walk::class,
            "method" => "post",
            "status" => 200,
            "path" => "/walks/epilogue",
            "security_post_denormalize" => "is_granted('".WalkVoter::READ."', object.walk)",
        ],
        "walk_create" => [
            "messenger" => "input",
            "input" => WalkCreateRequest::class,
            "output" => Walk::class,
            "method" => "post",
            "status" => 200,
            "path" => "/walks/create",
            "security_post_denormalize" => "is_granted('".TeamVoter::TEAM_READ."', object.team) and user.hasTeam(object.team)",
        ],
    ],
    itemOperations: [
        "get",
    ],
    attributes: [
        "pagination_items_per_page" => 5,
        "order" => ["teamName" => "ASC"],
    ],
    normalizationContext: ["groups" => ["walk:read"]]
)]
class Walk
{
    use AgeRangeField;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue()]
    #[ApiProperty(identifier: true)]
    private int $id;

    #[ORM\Column(type: 'string', length: 1024)]
    private string $name;

    /** @var Collection<int, WayPoint> **/
    #[ORM\OneToMany(mappedBy: 'walk', targetEntity: WayPoint::class)]
    private Collection $wayPoints;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $startTime;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $endTime;

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

    /** @var Collection<int, Guest> **/
    #[ORM\OneToMany(mappedBy: 'walk', targetEntity: Guest::class)]
    private Collection $guests;

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

    #[ORM\Column(type: 'boolean')]
    private bool $isWithContactsCount;

    public function __construct()
    {
        $this->ageRanges = [];
        $this->walkTags = new ArrayCollection();
        $this->walkTeamMembers = new ArrayCollection();
        $this->wayPoints = new ArrayCollection();
        $this->guests = new ArrayCollection();
        $this->holidays = false;
        $this->conceptOfDay = '';
        $this->commitments = '';
        $this->insights = '';
        $this->systemicAnswer = '';
        $this->walkReflection = '';
    }

    public static function fromWalkCreateRequest(WalkCreateRequest $request, SystemicQuestion $systemicQuestion): self
    {
        $instance = new self();
        $team = $request->team;

        $instance->setWalkTeamMembers(new ArrayCollection($request->walkTeamMembers));
        $instance->setTeamName($team->getName());
        $instance->setIsWithContactsCount($team->isWithContactsCount());
        $instance->updateClient($team->getClient());
        $instance->setName($request->name);
        $instance->setStartTime($request->startTime);
        $instance->setEndTime($request->startTime);
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
        $instance->setAgeRanges($team->getAgeRanges());

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

    /**
     * @return Collection<int,Guest>
     */
    #[Groups(['walk:read'])]
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    /**
     * @param Collection<int,Guest> $guests
     */
    public function setGuests(Collection $guests): void
    {
        $this->guests = $guests;
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
    public function getEndTime(): \DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): void
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
    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): void
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

    public function addGuest(Guest $guest): self
    {
        $this->guests[] = $guest;

        return $this;
    }

    public function removeGuest(Guest $guest): void
    {
        $this->guests->removeElement($guest);
    }

    #[Groups(['walk:read'])]
    public function getIsUnfinished(): bool
    {
        return '' === $this->getSystemicAnswer();
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
    #[SerializedName('isWithContactsCount')]
    public function isWithContactsCount(): bool
    {
        return $this->isWithContactsCount;
    }

    public function setIsWithContactsCount(bool $isWithContactsCount): void
    {
        $this->isWithContactsCount = $isWithContactsCount;
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
