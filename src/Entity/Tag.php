<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Walk", inversedBy="walkTags")
     *
     * @var Collection|Walk[]
     */
    private $walks;

    /**
     * @ORM\ManyToMany(targetEntity="WayPoint", inversedBy="wayPointTags")
     *
     * @var Collection|WayPoint[]
     */
    private $wayPoints;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $color;

    public function __construct()
    {
        $this->wayPoints = new ArrayCollection();
        $this->walks = new ArrayCollection();
        $this->color = '';
        $this->name = '';
    }

    public function __toString(): string
    {
        return \sprintf(
            '%s (%s)',
            $this->getName(),
            $this->getColor()
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function addWalk(Walk $walk): void
    {
        if (!$this->walks->contains($walk)) {
            $this->walks->add($walk);
        }
    }

    public function removeWalk(Walk $walk): void
    {
        if ($this->walks->contains($walk)) {
            $this->walks->removeElement($walk);
        }
    }

    /**
     * @return Collection|Walk[]
     */
    public function getWalks()
    {
        return $this->walks;
    }

    /**
     * @param Walk[]|Collection $walks
     */
    public function setWalks($walks): void
    {
        $this->walks = $walks;
    }

    public function addWayPoint(WayPoint $wayPoint): void
    {
        if (!$this->wayPoints->contains($wayPoint)) {
            $this->wayPoints->add($wayPoint);
        }
    }

    public function removeWayPoint(WayPoint $wayPoint): void
    {
        if ($this->wayPoints->contains($wayPoint)) {
            $this->wayPoints->removeElement($wayPoint);
        }
    }

    /**
     * @return Collection|WayPoint[]
     */
    public function getWayPoints()
    {
        return $this->wayPoints;
    }

    /**
     * @param Collection|WayPoint[] $wayPoints
     */
    public function setWayPoints($wayPoints): void
    {
        $this->wayPoints = $wayPoints;
    }
}
