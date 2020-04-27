<?php
declare(strict_types=1);

namespace App\Value;

use Webmozart\Assert\Assert;

final class PeopleCount
{
    public int $count;

    public function __construct(int $count)
    {
        $this->validateCount($count);
        $this->count = $count;
    }

    public static function fromInt(int $count): self
    {
        return new self($count);
    }

    public static function none(): self
    {
        return new self(0);
    }

    public function count(): int
    {
        return $this->count;
    }

    private function validateCount(int $count): void
    {
        Assert::greaterThanEq($count, 0);
    }
}
