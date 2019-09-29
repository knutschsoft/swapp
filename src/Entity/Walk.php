<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\Fields\AgeRangeField;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctrineORMWalkRepository")
 * @ORM\Table(name="walk")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 **/
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
     * @ORM\ManyToOne(targetEntity="SystemicQuestion", inversedBy="walks")
     *
     * @var SystemicQuestion
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

        $instance->setTeamName($team->getName());
        $instance->setName("");
        $instance->setStartTime(new \DateTime());
        $instance->setEndTime(new \DateTime());
        $instance->setRating(1);
        $instance->setSystemicAnswer("");
        $instance->setSystemicQuestion($systemicQuestion);
        $instance->setWalkReflection("");
        $instance->setWeather("");
        $instance->setIsResubmission(false);
        $instance->setHolidays(false);
        $instance->setCommitments("");
        $instance->setInsights("");
        $instance->setConceptOfDay("");
        $instance->setAgeRanges($team->getAgeRanges());

        return $instance;
    }

    /**
     * @return mixed
     */
    public function getConceptOfDay()
    {
        return $this->conceptOfDay;
    }

    /**
     * @return mixed
     */

    /**
     * @param mixed $conceptOfDay
     */
    public function setConceptOfDay($conceptOfDay): void
    {
        $this->conceptOfDay = $conceptOfDay;
    }

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
     * @return mixed
     */
    public function getSystemicQuestion()
    {
        return $this->systemicQuestion;
    }

    /**
     * @param mixed $systemicQuestion
     */
    public function setSystemicQuestion($systemicQuestion): void
    {
        $this->systemicQuestion = $systemicQuestion;
    }

    /**
     * @return mixed
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

    public function toArray(): array
    {
        return [
            $this->id,
            $this->name,
            $this->startTime->format('d.m.Y H:i:s'),
            $this->endTime->format('d.m.Y H:i:s'),
            $this->walkReflection,
            $this->rating,
            $this->systemicQuestion,
            $this->systemicAnswer,
            $this->insights,
            $this->commitments,
            $this->isResubmission,
            $this->weather,
            $this->holidays,
            $this->conceptOfDay,
            $this->getMalesCount(),
            $this->getFemalesCount(),
            $this->teamName,
//            $this->walkTeamMembers,
//            $this->guests,
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf(
            '%s',
            $this->getName()
        );
    }

    /**
     * @return mixed
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

    public function getFemalesCount(): int
    {
        $count = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $count += $wayPoint->getFemalesCount();
        }

        return $count;
    }

    public function getMalesCount(): int
    {
        $count = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $count += $wayPoint->getMalesCount();
        }

        return $count;
    }

    public function getQueerCount(): int
    {
        $count = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $count += $wayPoint->getQueerCount();
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

    public function addWayPoint(WayPoint $wayPoint): Walk
    {
        $this->wayPoints[] = $wayPoint;

        return $this;
    }

    public function removeWayPoint(WayPoint $wayPoint): void
    {
        $this->wayPoints->removeElement($wayPoint);
    }

    public function addWalkTeamMember(User $walkTeamMember): Walk
    {
        $this->walkTeamMembers[] = $walkTeamMember;

        return $this;
    }

    public function removeWalkTeamMember(User $walkTeamMember): void
    {
        $this->walkTeamMembers->removeElement($walkTeamMember);
    }

    public function addGuest(Guest $guest): Walk
    {
        $this->guests[] = $guest;

        return $this;
    }

    public function removeGuest(Guest $guest): void
    {
        $this->guests->removeElement($guest);
    }
}
