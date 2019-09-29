<?php
declare(strict_types=1);

namespace App\Value;

use Webmozart\Assert\Assert;

final class AgeRange
{
    /** @var int */
    public $rangeStart;

    /** @var int */
    public $rangeEnd;

    /**
     * @param mixed $rangeStart
     * @param mixed $rangeEnd
     */
    public function __construct($rangeStart, $rangeEnd)
    {
        Assert::integerish($rangeStart);
        Assert::integerish($rangeEnd);

        if ($rangeStart > $rangeEnd) {
            throw new \InvalidArgumentException('The start of a range has to be smaller than it\'s end.');
        }

        $this->rangeStart = (int) $rangeStart;
        $this->rangeEnd = (int) $rangeEnd;
    }

    public static function fromArray(array $count = []): self
    {
        $start = $count['start'] ?? ($count[0] ?? 0);
        $end = $count['end'] ?? ($count[1] ?? 0);

        return new self($start, $end);
    }

    public function getRangeStart(): int
    {
        return $this->rangeStart;
    }

    public function getRangeEnd(): int
    {
        return $this->rangeEnd;
    }

    public function equal(self $ageRange): bool
    {
        return $this->getRangeStart() === $ageRange->getRangeStart() && $this->getRangeEnd() === $ageRange->getRangeEnd();
    }
}
