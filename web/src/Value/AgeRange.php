<?php
declare(strict_types=1);

namespace App\Value;

use Symfony\Component\Serializer\Annotation\Groups;
use Webmozart\Assert\Assert;

class AgeRange
{
    private readonly int $rangeStart;

    private readonly int $rangeEnd;

    public function __construct(int|string $rangeStart, int|string $rangeEnd)
    {
        Assert::integerish($rangeStart);
        Assert::integerish($rangeEnd);

        if ($rangeStart > $rangeEnd) {
            throw new \InvalidArgumentException('The start of a range has to be smaller than it\'s end.');
        }

        $this->rangeStart = (int) $rangeStart;
        $this->rangeEnd = (int) $rangeEnd;
    }

    /**
     * @param array<int|string, int|string> $count
     *
     * @return self
     */
    public static function fromArray(array $count = []): self
    {
        $start = $count['start'] ?? ($count[0] ?? 0);
        $end = $count['end'] ?? ($count[1] ?? 0);

        return new self($start, $end);
    }

    public static function fromString(string $ageRangeString): self
    {
        $ageRangeString = \trim($ageRangeString);
        Assert::contains($ageRangeString, '-', \sprintf('Invalid value given cause there is no "%s" in "%s"', '-', $ageRangeString));
        $parts = \explode('-', $ageRangeString);
        Assert::count($parts, 2, \sprintf('Too many parts after explode of "%s"', $ageRangeString));

        return new self(\trim($parts[0]), \trim($parts[1]));
    }

    #[Groups(['walk:read', 'team:read', 'wayPoint:read'])]
    public function getRangeStart(): int
    {
        return $this->rangeStart;
    }

    #[Groups(['walk:read', 'team:read', 'wayPoint:read'])]
    public function getRangeEnd(): int
    {
        return $this->rangeEnd;
    }

    public function equal(self $ageRange): bool
    {
        return $this->getRangeStart() === $ageRange->getRangeStart() && $this->getRangeEnd() === $ageRange->getRangeEnd();
    }

    #[Groups(['walk:read', 'team:read', 'wayPoint:read'])]
    public function getFrontendLabel(): string
    {
        return \sprintf('%d - %d', $this->rangeStart, $this->rangeEnd);
    }
}
