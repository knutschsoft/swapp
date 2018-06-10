<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="DoctrineORMAgeRangeRepository")
 * @ORM\Table(name="age_range")
 */
final class AgeRange
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
    private $rangeStart;

    /**
     * @ORM\Column(type="smallint")
     * @var int
     */
    private $rangeEnd;

    private function __construct($rangeStart, $rangeEnd)
    {
        $this->setRangeStart($rangeStart);
        $this->setRangeEnd($rangeEnd);
    }

    public static function fromArray(array $count = [])
    {
        $start = isset($count['start']) ? $count['start'] : isset($count[0]) ? $count[0] : 0;
        $end = isset($count['end']) ? $count['end'] : isset($count[1]) ? $count[1] : 0;

        return new self($start, $end);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRangeStart()
    {
        return $this->rangeStart;
    }

    public function getRangeEnd()
    {
        return $this->rangeEnd;
    }

    public function setRangeStart($rangeStart)
    {
        $this->rangeStart = $rangeStart;
    }

    public function setRangeEnd($rangeEnd)
    {
        $this->rangeEnd = $rangeEnd;
    }

    private function isPositiveInt($number)
    {
        return is_int($number) && (int)$number >= 0;
    }
}
