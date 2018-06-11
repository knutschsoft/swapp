<?php

namespace AppBundle\Value;

final class AgeGroup
{
    /** @var AgeGroupRange */
    private $ageRange;
    /** @var GenderCount */
    private $count;

    private function __construct($range, $count)
    {
        $this->ageRange = $range;
        $this->count = $count;
    }

    public static function fromRangeAndCount(AgeGroupRange $range, GenderCount $count)
    {
        return new self($range, $count);
    }

    public function ageRange()
    {
        return $this->ageRange;
    }

    public function count()
    {
        return $this->count;
    }
}
