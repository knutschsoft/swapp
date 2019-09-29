<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctrineORMTagRepository")
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
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Walk", inversedBy="walkTags")
     *
     * @var ArrayCollection
     */
    private $walks;

    /**
     * @ORM\ManyToMany(targetEntity="WayPoint", inversedBy="wayPointTags")
     *
     * @var ArrayCollection
     */
    private $wayPoints;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
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
    public function __toString(): string
    {
        return \sprintf(
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
    public function setName($name): void
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
    public function setColor($color): void
    {
        $this->color = $color;
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
     * @param Walk $walk
     */
    public function addWalk(Walk $walk): void
    {
        if (!$this->walks->contains($walk)) {
            $this->walks->add($walk);
        }
    }

    /**
     * @param Walk $walk
     */
    public function removeWalk(Walk $walk): void
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
    public function setWalks($walks): void
    {
        $this->walks = $walks;
    }


    /**
     * @param WayPoint $wayPoint
     */
    public function addWayPoint(WayPoint $wayPoint): void
    {
        if (!$this->wayPoints->contains($wayPoint)) {
            $this->wayPoints->add($wayPoint);
        }
    }

    /**
     * @param WayPoint $wayPoint
     */
    public function removeWayPoint(WayPoint $wayPoint): void
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
    public function setWayPoints($wayPoints): void
    {
        $this->wayPoints = $wayPoints;
    }
}
