<?php
declare(strict_types=1);

namespace App\Value;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Webmozart\Assert\Assert;

/**
 * @ApiResource()
 */
class AgeRange
{
    public int $rangeStart;

    public int $rangeEnd;

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

    /**
     * @return int
     *
     * @Groups({"walk:read", "team:read"})
     */
    public function getRangeStart(): int
    {
        return $this->rangeStart;
    }

    /**
     * @return int
     *
     * @Groups({"walk:read", "team:read"})
     */
    public function getRangeEnd(): int
    {
        return $this->rangeEnd;
    }

    public function equal(self $ageRange): bool
    {
        return $this->getRangeStart() === $ageRange->getRangeStart() && $this->getRangeEnd() === $ageRange->getRangeEnd();
    }
}
