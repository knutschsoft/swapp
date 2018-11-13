<?php

namespace AppBundle\Value;

use Webmozart\Assert\Assert;

final class PeopleCount
{
    /** @var int */
    public $count;

    public function __construct(int $count)
    {
        $this->validateCount($count);
        $this->count = $count;
    }

    public static function fromInt(int $count)
    {
        return new self($count);
    }

    public function count(): int
    {
        return $this->count;
    }

    private function validateCount($count): void
    {
        Assert::greaterThanEq($count, 0);
    }
}
