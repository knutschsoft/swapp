<?php

namespace AppBundle\Value;


class AgeGroupRange
{
    /** @var int */
    protected $rangeStart;

    /**@var int */
    protected $rangeEnd;

    private function __construct($rangeStart, $rangeEnd)
    {
        if (!$this->isPositiveInt($rangeStart) || !$this->isPositiveInt($rangeEnd)) {
            throw new \InvalidArgumentException('A range must end with positive Integer.');
        }
        if ($rangeStart > $rangeEnd) {
            throw new \InvalidArgumentException('The start of a range has to be smaller than it\'s end.');
        }

        $this->rangeStart = $rangeStart;
        $this->rangeEnd = $rangeEnd;
    }

    protected static function fromArray(array $count = [])
    {
        $start = isset($count['start']) ? $count['start'] : isset($count[0]) ? $count[0] : 0;
        $end = isset($count['end']) ? $count['end'] : isset($count[1]) ? $count[1] : 0;

        return new self($start, $end);
    }

    public function getRangeStart()
    {
        return $this->rangeStart;
    }

    public function getRangeEnd()
    {
        return $this->rangeEnd;
    }

    protected function isPositiveInt($number)
    {
        return is_int($number) && (int)$number >= 0;
    }
}
