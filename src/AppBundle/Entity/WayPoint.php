<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineORMWayPointRepository")
 * @ORM\Table(name="way_point")
 */
class WayPoint
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Walk", inversedBy="wayPoints")
     */
    protected $walk;

    /**
     * @ORM\Column(type="string", length=4096)
     * @Assert\NotBlank()
     */
    protected $locationName;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    protected $ageRangeStart;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    protected $ageRangeEnd;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    protected $malesCount;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    protected $femalesCount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $note;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $isMeeting;

    /**
     * @return mixed
     */
    public function getIsMeeting()
    {
        return $this->isMeeting;
    }

    /**
     * @param mixed $isMeeting
     */
    public function setIsMeeting($isMeeting)
    {
        $this->isMeeting = $isMeeting;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '%s (%s-%s)',
            $this->getLocationName(),
            $this->getAgeRangeStart(),
            $this->getAgeRangeEnd()
        );
    }

    /**
     * @param mixed $ageRangeEnd
     */
    public function setAgeRangeEnd($ageRangeEnd)
    {
        $this->ageRangeEnd = $ageRangeEnd;
    }

    /**
     * @return mixed
     */
    public function getAgeRangeEnd()
    {
        return $this->ageRangeEnd;
    }

    /**
     * @param mixed $ageRangeStart
     */
    public function setAgeRangeStart($ageRangeStart)
    {
        $this->ageRangeStart = $ageRangeStart;
    }

    /**
     * @return mixed
     */
    public function getAgeRangeStart()
    {
        return $this->ageRangeStart;
    }

    /**
     * @param mixed $femalesCount
     */
    public function setFemalesCount($femalesCount)
    {
        $this->femalesCount = $femalesCount;
    }

    /**
     * @return mixed
     */
    public function getFemalesCount()
    {
        return $this->femalesCount;
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
     * @param mixed $locationName
     */
    public function setLocationName($locationName)
    {
        $this->locationName = $locationName;
    }

    /**
     * @return mixed
     */
    public function getLocationName()
    {
        return $this->locationName;
    }

    /**
     * @param mixed $malesCount
     */
    public function setMalesCount($malesCount)
    {
        $this->malesCount = $malesCount;
    }

    /**
     * @return mixed
     */
    public function getMalesCount()
    {
        return $this->malesCount;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $walk
     */
    public function setWalk($walk)
    {
        $this->walk = $walk;
    }

    /**
     * @return mixed
     */
    public function getWalk()
    {
        return $this->walk;
    }
}
