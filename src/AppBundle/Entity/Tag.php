<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineORMTagRepository")
 * @ORM\Table(name="tag")
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Walk", inversedBy="walkTags")
     * @var ArrayCollection
     */
    private $walks;

    /**
     * @ORM\ManyToMany(targetEntity="WayPoint", inversedBy="wayPointTags")
     * @var ArrayCollection
     */
    private $wayPoints;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $color;

    public function __construct()
    {
        $this->wayPoints = new ArrayCollection();
        $this->walks = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '%s (%s)',
            $this->getName(),
            $this->getColor()
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
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @param Walk $walk
     */
    public function addWalk(Walk $walk)
    {
        if (!$this->walks->contains($walk)) {
            $this->walks->add($walk);
        }
    }

    /**
     * @param Walk $walk
     */
    public function removeWalk(Walk $walk)
    {
        if ($this->walks->contains($walk)) {
            $this->walks->removeElement($walk);
        }
    }

    /**
     * @return mixed
     */
    public function getWalks()
    {
        return $this->walks;
    }

    /**
     * @param mixed $walks
     */
    public function setWalks($walks)
    {
        $this->walks = $walks;
    }


    /**
     * @param WayPoint $wayPoint
     */
    public function addWayPoint(WayPoint $wayPoint)
    {
        if (!$this->wayPoints->contains($wayPoint)) {
            $this->wayPoints->add($wayPoint);
        }
    }

    /**
     * @param WayPoint $wayPoint
     */
    public function removeWayPoint(WayPoint $wayPoint)
    {
        if ($this->wayPoints->contains($wayPoint)) {
            $this->wayPoints->removeElement($wayPoint);
        }
    }

    /**
     * @return mixed
     */
    public function getWayPoints()
    {
        return $this->wayPoints;
    }

    /**
     * @param mixed $wayPoints
     */
    public function setWayPoints($wayPoints)
    {
        $this->wayPoints = $wayPoints;
    }
}
