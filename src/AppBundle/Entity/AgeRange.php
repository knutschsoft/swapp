<?php

namespace AppBundle\Entity;

use AppBundle\Value\AgeGroupRange;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineORMAgeRangeRepository")
 * @ORM\Table(name="age_range")
 */
class AgeRange extends AgeGroupRange
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\Column(type="smallint")
     * @var int
     */
    protected $rangeStart;

    /**
     * @ORM\Column(type="smallint")
     * @var int
     */
    protected $rangeEnd;

    public function __construct($rangeStart, $rangeEnd)
    {
        parent::fromArray([$rangeStart, $rangeEnd]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setRangeStart($rangeStart)
    {
        $this->rangeStart = $rangeStart;
    }

    public function setRangeEnd($rangeEnd)
    {
        $this->rangeEnd = $rangeEnd;
    }

}
