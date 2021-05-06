<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Dto\WalkExportRequest;
use App\Dto\WalkPrologueRequest;
use App\Entity\Fields\AgeRangeField;
use App\Security\Voter\TeamVoter;
use App\Value\AgeRange;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctrineORMWalkRepository")
 * @ORM\Table(name="walk")
 **/
#[ApiFilter(OrderFilter::class, properties: ["name", "rating", "teamName", "startTime", "endTime", "isResubmission"])]
#[ApiResource(
    collectionOperations: [
    "get",
    "walk_export" => [
        "messenger" => "input",
        "input_formats" => [
            "csv" => ["text/csv"],
        ],
        "openapi_context" => [
            "summary" => "Exports all walks",
        ],
        "input" => WalkExportRequest::class,
        "output" => false,
        "status" => 200,
        "method" => "post",
        "path" => "/walks/export",
    ],
    "walk_prologue" => [
        "messenger" => "input",
        "input" => WalkPrologueRequest::class,
        "output" => Walk::class,
        "method" => "post",
        "status" => 200,
        "path" => "/walks/prologue",
        "security_post_denormalize" => '(is_granted("'.TeamVoter::TEAM_READ.'", object.team) and user.hasTeam(object.team))',
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
     *
     * @var int
     */
    #[ApiProperty(identifier: true)]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(groups={"prologue", "registration"})
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="WayPoint", mappedBy="walk")
     *
     * @var Collection|WayPoint[]
     **/
    private $wayPoints;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotBlank(groups={"prologue", "registration"})
     *
     * @var \DateTime
     */
    private $startTime;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotBlank(groups={"registration"})
     *
     * @var \DateTime
     */
    private $endTime;

    /**
     * @ORM\Column(type="string", length=4096)
     *
     * @Assert\NotBlank(groups={"registration"})
     *
     * @var string
     */
    private $walkReflection;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="walks", cascade={"all"}, orphanRemoval=true)
     *
     * @MaxDepth(1)
     *
     * @var Collection|User[]
     */
    private $walkTeamMembers;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="walks")
     *
     * @var ArrayCollection|Tag[]
     */
    private $walkTags;

    /**
     * @ORM\Column(type="smallint")
     *
     * @Assert\NotBlank(groups={"registration"})
     *
     * @var int
     */
    private $rating;

    /**
     * @ORM\Column(type="string", length=4096)
     *
     * @var string
     */
    private $systemicQuestion;

    /**
     * @ORM\Column(type="string", length=4096)
     *
     * @Assert\NotBlank(groups={"registration"})
     *
     * @var string
     */
    private $systemicAnswer;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var ?string
     */
    private $insights;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var ?string
     */
    private $commitments;

    /**
     * @ORM\Column(type="boolean", length=255)
     *
     * @var bool
     */
    private $isResubmission;
    /**
     * @ORM\OneToMany(targetEntity="Guest", mappedBy="walk")
     *
     * @var ArrayCollection|Guest[]
     **/
    private $guests;
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(groups={"prologue"})
     *
     * @var string
     */
    private $weather;
    /**
     * @ORM\Column(type="boolean", length=255)
     *
     * @var bool
     */
    private $holidays;
    /**
     * @ORM\Column(type="string", length=4096)
     *
     * @Assert\NotBlank(groups={"prologue"})
     *
     * @var string
     */
    private $conceptOfDay;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(groups={"prologue"})
     *
     * @var string
     */
    private $teamName;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     *
     * @var ?\DateTime
     */
    private $deletedAt;

    public function __construct()
    {
        $this->ageRanges = [];
        $this->walkTags = new ArrayCollection();
        $this->walkTeamMembers = new ArrayCollection();
        $this->wayPoints = new ArrayCollection();
    }

    public static function prologue(Team $team, SystemicQuestion $systemicQuestion): self
    {
        $instance = new self();

        // CreateWalkPrologueController::class;
        $instance->setTeamName($team->getName());
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

    /**
     * @return mixed
     *
     * @Groups({"walk:read"})
     */
    public function getConceptOfDay()
    {
        return $this->conceptOfDay;
    }

    /**
     * @param mixed $conceptOfDay
     */
    public function setConceptOfDay($conceptOfDay): void
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
     * @return mixed
     *
     * @Groups({"walk:read"})
     */
    public function getCommitments()
    {
        return $this->commitments;
    }

    /**
     * @param mixed $commitments
     */
    public function setCommitments($commitments): void
    {
        $this->commitments = $commitments;
    }

    /**
     * @return mixed
     *
     * @Groups({"walk:read"})
     */
    public function getIsResubmission()
    {
        return $this->isResubmission;
    }

    /**
     * @param mixed $isResubmission
     */
    public function setIsResubmission($isResubmission): void
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
     * @return mixed
     *
     * @Groups({"walk:read"})
     */
    public function getWeather()
    {
        return $this->weather;
    }

    /**
     * @param mixed $weather
     */
    public function setWeather($weather): void
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

    /**
     * @param bool $holidays
     */
    public function setHolidays(bool $holidays): void
    {
        $this->holidays = $holidays;
    }

    /**
     * @return mixed
     *
     * @Groups({"walk:read"})
     */
    public function getGuests()
    {
        return $this->guests;
    }

    /**
     * @param mixed $guests
     */
    public function setGuests($guests): void
    {
        $this->guests = $guests;
    }

    public function __toString(): string
    {
        return \sprintf(
            '%s',
            $this->getName()
        );
    }

    /**
     * @return mixed
     *
     * @Groups({"walk:read"})
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     *
     * @Groups({"walk:read"})
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return mixed
     *
     * @Groups({"walk:read"})
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     *
     * @Groups({"walk:read"})
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     */
    public function setRating($rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return mixed
     *
     * @Groups({"walk:read"})
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime): void
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

    /**
     * @param string $systemicAnswer
     */
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
     * @return mixed
     *
     * @Groups({"walk:read"})
     */
    public function getWalkTags()
    {
        return $this->walkTags;
    }

    /**
     * @param mixed $walkTags
     */
    public function setWalkTags($walkTags): void
    {
        $this->walkTags = $walkTags;
    }

    /**
     * @return mixed
     *
     * @Groups({"walk:read"})
     */
    public function getWalkReflection()
    {
        return $this->walkReflection;
    }

    /**
     * @param mixed $walkReflection
     */
    public function setWalkReflection($walkReflection): void
    {
        $this->walkReflection = $walkReflection;
    }

    /**
     * @return mixed
     *
     * @Groups({"walk:read"})
     */
    public function getWalkTeamMembers()
    {
        return $this->walkTeamMembers;
    }

    /**
     * @param mixed $walkTeamMembers
     */
    public function setWalkTeamMembers($walkTeamMembers): void
    {
        $this->walkTeamMembers = $walkTeamMembers;
    }

    /**
     * @return WayPoint[]|Collection
     *
     * @Groups({"walk:read"})
     */
    public function getWayPoints(): Collection
    {
        return $this->wayPoints;
    }

    /**
     * @param WayPoint[]|Collection $wayPoints
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

    /**
     * @param string $teamName
     */
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
}
