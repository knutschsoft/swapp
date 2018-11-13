<?php

namespace AppBundle\Value;

use Webmozart\Assert\Assert;

final class Gender
{
    /** @var string */
    public $gender;

    public function __construct(string $gender)
    {
        Assert::oneOf($gender, ['w', 'm', '*']);
        $this->gender = $gender;
    }

    public static function fromString(string $gender)
    {
        return new self($gender);
    }

    public function gender(): string
    {
        return $this->gender;
    }

    public function isMale(): bool
    {
        return $this->gender === 'm';
    }

    public function isFemale(): bool
    {
        return $this->gender === 'w';
    }

    public function isQueer(): bool
    {
        return $this->gender === '*';
    }

    public function equal(Gender $gender): bool
    {
        return $this->gender() === $gender->gender();
    }
}
