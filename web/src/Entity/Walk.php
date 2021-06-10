<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Dto\WalkExportRequest;
use App\Dto\WalkPrologueRequest;
use App\Dto\WayPointAddRequest;
use App\Entity\Fields\AgeRangeField;
use App\Security\Voter\TeamVoter;
use App\Value\AgeRange;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctrineORMWalkRepository")
 * @ORM\Table(name="walk")
 **/
#[ApiFilter(OrderFilter::class, properties: ["name", "rating", "teamName", "startTime", "endTime", "isResubmission"])]
#[ApiFilter(BooleanFilter::class, properties: ["isResubmission"])]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
#[ApiResource(
    collectionOperations: [
    "get",
    "walk_export" => [
        "messenger" => "input",
        "openapi_context" => [
            "summary" => "Exports all walks",
        ],
        "input" => WalkExportRequest::class,
        "output" => Response::class,
        "status" => 200,
        "method" => "post",
        "path" => "/walks/export",
        "security_post_denormalize" => "is_granted('CLIENT_READ', object.client)",
    ],
    "walk_prologue" => [
        "messenger" => "input",
        "input" => WalkPrologueRequest::class,
        "output" => Walk::class,
        "method" => "post",
        "status" => 200,
        "path" => "/walks/prologue",
        "security_post_denormalize" => "is_granted('".TeamVoter::TEAM_READ."', object.team) and user.hasTeam(object.team)",
    ],
    "add_way_point" => [
        "messenger" => "input",
        "input" => WayPointAddRequest::class,
        "output" => Walk::class,
        "method" => "post",
        "status" => 200,
        "path" => "/walks/add-way-point",
    ],
    ],
    itemOperations: [
    "get",
    ],
    attributes: ["pagination_items_per_page" => 5],
    normalizationContext: ["groups" => ["walk:read"]]
)]
class Walk
{
    use AgeRangeField;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[ApiProperty(identifier: true)]
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(groups={"prologue", "registration"})
     */
    private string $name;

    /**
     * @ORM\OneToMany(targetEntity="WayPoint", mappedBy="walk")
     *
     * @var Collection<int, WayPoint>
     **/
    private Collection $wayPoints;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotBlank(groups={"prologue", "registration"})
     */
    private \DateTime $startTime;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotBlank(groups={"registration"})
     */
    private \DateTime $endTime;

    /**
     * @ORM\Column(type="string", length=4096)
     *
     * @Assert\NotBlank(groups={"registration"})
     */
    private string $walkReflection;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="walks", cascade={"all"}, orphanRemoval=true)
     *
     * @MaxDepth(1)
     *
     * @var Collection<int, User>
     */
    private Collection $walkTeamMembers;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="walks")
     *
     * @var Collection<int, Tag>
     */
    private Collection $walkTags;

    /**
     * @ORM\Column(type="smallint")
     *
     * @Assert\NotBlank(groups={"registration"})
     */
    private int $rating;

    /** @ORM\Column(type="string", length=4096) */
    private string $systemicQuestion;

    /**
     * @ORM\Column(type="string", length=4096)
     *
     * @Assert\NotBlank(groups={"registration"})
     */
    private string $systemicAnswer;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var ?string
     */
    private ?string $insights = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var ?string
     */
    private ?string $commitments = null;

    /** @ORM\Column(type="boolean", length=255) */
    private bool $isResubmission;
    /**
     * @ORM\OneToMany(targetEntity="Guest", mappedBy="walk")
     *
     * @var Collection<int, Guest>
     **/
    private Collection $guests;
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(groups={"prologue"})
     */
    private string $weather;
    /** @ORM\Column(type="boolean", length=255) */
    private bool $holidays;
    /**
     * @ORM\Column(type="string", length=4096)
     *
     * @Assert\NotBlank(groups={"prologue"})
     */
    private string $conceptOfDay;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(groups={"prologue"})
     */
    private string $teamName;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     *
     * @var ?\DateTime
     */
    private ?\DateTime $deletedAt = null;

    /** @ORM\ManyToOne(targetEntity="Client", inversedBy="walks") */
    private Client $client;

    public function __construct()
    {
        $this->ageRanges = [];
        $this->walkTags = new ArrayCollection();
        $this->walkTeamMembers = new ArrayCollection();
        $this->wayPoints = new ArrayCollection();
        $this->guests = new ArrayCollection();
        $this->holidays = false;
        $this->conceptOfDay = '';
    }

    public static function prologue(Team $team, SystemicQuestion $systemicQuestion): self
    {
        $instance = new self();

        $instance->setTeamName($team->getName());
        $instance->updateClient($team->getClient());
        $instance->setName('');
        $instance->setStartTime(new \DateTime());
        $instance->setEndTime(new \DateTime());
        $instance->setRating(1);
        $instance->setSystemicAnswer('');
        $instance->setSystemicQuestion($systemicQuestion->getQuestion());
        $instance->setWalkReflection('');
        $instance->setWeather('');
        $instance->setIsResubmission(false);
        $instance->setHolidays(false);
        $instance->setCommitments('');
        $instance->setInsights('');
        $instance->setConceptOfDay('');
        $instance->setAgeRanges($team->getAgeRanges());

        return $instance;
    }

    public function newPrologue(Team $team, SystemicQuestion $systemicQuestion): void
    {
        $this->setTeamName($team->getName());
        $this->setEndTime(new \DateTime());
        $this->setRating(1);
        $this->setSystemicAnswer('');
        $this->setSystemicQuestion($systemicQuestion->getQuestion());
        $this->setWalkReflection('');
        $this->setIsResubmission(false);
        $this->setCommitments('');
        $this->setInsights('');
        $this->setAgeRanges($team->getAgeRanges());
    }

    /**
     * @return string
     *
     * @Groups({"walk:read"})
     */
    public function getConceptOfDay(): string
    {
        return $this->conceptOfDay;
    }

    public function setConceptOfDay(string $conceptOfDay): void
    {
        $this->conceptOfDay = $conceptOfDay;
    }

    /**
     * @return string|null
     *
     * @Groups({"walk:read"})
     */
    public function getInsights(): ?string
    {
        return $this->insights;
    }

    public function setInsights(?string $insights): void
    {
        $this->insights = $insights;
    }

    /**
     * @return ?string
     *
     * @Groups({"walk:read"})
     */
    public function getCommitments(): ?string
    {
        return $this->commitments;
    }

    /**
     * @param ?string $commitments
     */
    public function setCommitments(?string $commitments): void
    {
        $this->commitments = $commitments;
    }

    /**
     * @return bool
     *
     * @Groups({"walk:read"})
     */
    public function getIsResubmission(): bool
    {
        return $this->isResubmission;
    }

    public function setIsResubmission(bool $isResubmission): void
    {
        $this->isResubmission = $isResubmission;
    }

    /**
     * @return string
     *
     * @Groups({"walk:read"})
     */
    public function getSystemicQuestion(): string
    {
        return $this->systemicQuestion;
    }

    public function setSystemicQuestion(string $systemicQuestion): void
    {
        $this->systemicQuestion = $systemicQuestion;
    }

    /**
     * @return string
     *
     * @Groups({"walk:read"})
     */
    public function getWeather(): string
    {
        return $this->weather;
    }

    public function setWeather(string $weather): void
    {
        $this->weather = $weather;
    }

    /**
     * @return bool
     *
     * @Groups({"walk:read"})
     */
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
     *
     * @Groups({"walk:read"})
     */
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

    /**
     * @return string
     *
     * @Groups({"walk:read"})
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \DateTime
     *
     * @Groups({"walk:read"})
     */
    public function getEndTime(): \DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return int
     *
     * @Groups({"walk:read"})
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
     * @return int
     *
     * @Groups({"walk:read"})
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return \DateTime
     *
     * @Groups({"walk:read"})
     */
    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return string
     *
     * @Groups({"walk:read"})
     */
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
     *
     * @Groups({"walk:read"})
     */
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

    /**
     * @return string
     *
     * @Groups({"walk:read"})
     */
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
     *
     * @Groups({"walk:read"})
     */
    public function getWalkTeamMembers(): Collection
    {
        return $this->walkTeamMembers;
    }

    /**
     * @param Collection<int,User> $walkTeamMembers
     */
    public function setWalkTeamMembers(Collection $walkTeamMembers): void
    {
        $this->walkTeamMembers = $walkTeamMembers;
    }

    /**
     * @return Collection<int, WayPoint>
     *
     * @Groups({"walk:read"})
     */
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

    /**
     * @return string
     *
     * @Groups({"walk:read"})
     */
    public function getTeamName(): string
    {
        return $this->teamName;
    }

    public function setTeamName(string $teamName): void
    {
        $this->teamName = $teamName;
    }

    /**
     * @return int
     *
     * @Groups({"walk:read"})
     */
    public function getFemalesCount(): int
    {
        $count = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $count += $wayPoint->getFemalesCount();
        }

        return $count;
    }

    /**
     * @return int
     *
     * @Groups({"walk:read"})
     */
    public function getMalesCount(): int
    {
        $count = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $count += $wayPoint->getMalesCount();
        }

        return $count;
    }

    /**
     * @return int
     *
     * @Groups({"walk:read"})
     */
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

    /**
     * @return bool
     *
     * @Groups({"walk:read"})
     */
    public function getIsUnfinished(): bool
    {
        return '' === $this->getSystemicAnswer();
    }

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
}
