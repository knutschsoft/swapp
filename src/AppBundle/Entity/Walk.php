<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineORMWalkRepository")
 * @ORM\Table(name="walk")
 **/
class Walk
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="WayPoint", mappedBy="walk")
     **/
    protected $wayPoints;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    protected $startTime;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    protected $endTime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $walkReflection;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="walks")
     */
    protected $walkTeamMembers;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="walks")
     */
    protected $tags;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    protected $rating;

    /**
     * @ORM\Column(type="string", length=4096)
     */
    protected $systemicQuestion;

    /**
     * @ORM\Column(type="string", length=4096)
     * @Assert\NotBlank()
     */
    protected $systemicAnswer;

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
     * @return mixed
     */
    public function getHolidays()
    {
        return $this->holidays;
    }

    /**
     * @param mixed $holidays
     */
    public function setHolidays($holidays)
    {
        $this->holidays = $holidays;
    }

    /**
     * @ORM\OneToMany(targetEntity="Guest", mappedBy="walk")
     **/
    protected $guests;

    /**
     * @ORM\Column(type="boolean", length=255)
     */
    protected $isInternal;

    /**
     * @ORM\Column(type="string", length=4096)
     */
    protected $weather;

    /**
     * @ORM\Column(type="string", length=4096)
     */
    protected $holidays;

    /**
     * @return boolean
     */
    public function getIsInternal()
    {
        return $this->isInternal;
    }

    /**
     * @param boolean $isInternal
     */
    public function setIsInternal($isInternal)
    {
        $this->isInternal = $isInternal;
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
     * @param mixed $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
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
    public function getId()
    {
        return $this->id;
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
    public function getName()
    {
        return $this->name;
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
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param string $systemicAnswer
     */
    public function setSystemicAnswer($systemicAnswer)
    {
        $this->systemicAnswer = $systemicAnswer;
    }

    /**
     * @return string
     */
    public function getSystemicAnswer()
    {
        return $this->systemicAnswer;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
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
    public function getWalkReflection()
    {
        return $this->walkReflection;
    }

    /**
     * @param mixed $walkTeamMembers
     */
    public function setWalkTeamMembers($walkTeamMembers)
    {
        $this->walkTeamMembers = $walkTeamMembers;
    }

    /**
     * @return mixed
     */
    public function getWalkTeamMembers()
    {
        return $this->walkTeamMembers;
    }

    /**
     * @param mixed $wayPoints
     */
    public function setWayPoints($wayPoints)
    {
        $this->wayPoints = $wayPoints;
    }

    /**
     * @return mixed
     */
    public function getWayPoints()
    {
        return $this->wayPoints;
    }
}
