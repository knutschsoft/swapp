<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineORMWalkRepository")
 * @ORM\Table(name="walk")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 **/
class Walk
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"default", "registration"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="WayPoint", mappedBy="walk")
     **/
    private $wayPoints;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(groups={"default", "registration"})
     */
    private $startTime;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(groups={"default"})
     */
    private $endTime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $walkReflection;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="walks", cascade={"all"}, orphanRemoval=true)
     */
    private $walkTeamMembers;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="walks")
     * @var ArrayCollection
     */
    private $walkTags;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank(groups={"default"})
     */
    private $rating;

    /**
     * @ORM\ManyToOne(targetEntity="SystemicQuestion", inversedBy="walks")
     */
    private $systemicQuestion;

    /**
     * @ORM\Column(type="string", length=4096)
     * @Assert\NotBlank(groups={"default"})
     */
    private $systemicAnswer;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $insights;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $commitments;

    /**
     * @ORM\Column(type="boolean", length=255)
     */
    private $isResubmission;
    /**
     * @ORM\OneToMany(targetEntity="Guest", mappedBy="walk")
     **/
    private $guests;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"registration"})
     */
    private $weather;
    /**
     * @ORM\Column(type="boolean", length=255)
     * @Assert\NotBlank(groups={"registration"})
     */
    private $holidays;
    /**
     * @ORM\Column(type="string", length=4096)
     * @Assert\NotBlank(groups={"default", "registration"})
     */
    private $conceptOfDay;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $teamName;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    public function __construct()
    {
        $this->walkTags = new ArrayCollection();
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
    public function setConceptOfDay($conceptOfDay)
    {
        $this->conceptOfDay = $conceptOfDay;
    }

    public function getInsights()
    {
        return $this->insights;
    }

    /**
     * @param mixed $insights
     */
    public function setInsights($insights)
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
    public function setCommitments($commitments)
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
    public function setIsResubmission($isResubmission)
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
    public function setSystemicQuestion($systemicQuestion)
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
    public function setWeather($weather)
    {
        $this->weather = $weather;
    }

    /**
     * @return boolean
     */
    public function getHolidays()
    {
        return $this->holidays;
    }

    /**
     * @param boolean $holidays
     */
    public function setHolidays($holidays)
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
    public function setGuests($guests)
    {
        $this->guests = $guests;
    }

    public function toArray()
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
    public function __toString()
    {
        return sprintf(
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
    public function setName($name)
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
    public function setEndTime($endTime)
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
    public function setId($id)
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
    public function setRating($rating)
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
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return string
     */
    public function getSystemicAnswer()
    {
        return $this->systemicAnswer;
    }

    /**
     * @param string $systemicAnswer
     */
    public function setSystemicAnswer($systemicAnswer)
    {
        $this->systemicAnswer = $systemicAnswer;
    }


    public function addWalkTag(Tag $tag)
    {
        $tag->addWalk($this);
        $this->walkTags->add($tag);
    }

    public function removeWalkTag(Tag $tag)
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
    public function setWalkTags($walkTags)
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
    public function setWalkReflection($walkReflection)
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
    public function setWalkTeamMembers($walkTeamMembers)
    {
        $this->walkTeamMembers = $walkTeamMembers;
    }

    /**
     * @return WayPoint[]
     */
    public function getWayPoints()
    {
        return $this->wayPoints;
    }

    /**
     * @param WayPoint[] $wayPoints
     */
    public function setWayPoints($wayPoints)
    {
        $this->wayPoints = $wayPoints;
    }

    /**
     * @return string
     */
    public function getTeamName()
    {
        return $this->teamName;
    }

    /**
     * @param string $teamName
     */
    public function setTeamName($teamName)
    {
        $this->teamName = $teamName;
    }

    /**
     * @return int
     */
    public function getFemalesCount()
    {
        $count = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $count += $wayPoint->getFemalesCount();
        }

        return $count;
    }
    /**
     * @return int
     */
    public function getMalesCount()
    {
        $count = 0;
        foreach ($this->getWayPoints() as $wayPoint) {
            $count += $wayPoint->getMalesCount();
        }

        return $count;
    }

    /**
     * @return null|string
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param null|string $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }
}
