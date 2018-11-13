<?php

namespace AppBundle\Value;

use Webmozart\Assert\Assert;

final class AgeRange
{
    /** @var int */
    public $rangeStart;

    /**@var int */
    public $rangeEnd;

    public function __construct($rangeStart, $rangeEnd)
    {
        Assert::integerish($rangeStart);
        Assert::integerish($rangeEnd);

        if ($rangeStart > $rangeEnd) {
            throw new \InvalidArgumentException('The start of a range has to be smaller than it\'s end.');
        }

        $this->rangeStart = (int)$rangeStart;
        $this->rangeEnd = (int)$rangeEnd;
    }

    public static function fromArray(array $count = [])
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

    public function equal(AgeRange $ageRange): bool
    {
        return $this->getRangeStart() === $ageRange->getRangeStart() && $this->getRangeEnd() === $ageRange->getRangeEnd();
    }
}
