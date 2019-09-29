<?php

declare(strict_types=1);

namespace App\Value;

use Webmozart\Assert\Assert;

final class Gender
{
    /** @var string */
    public $gender;

    private const GENDER_QUEER = 'x';

    private const GENDER_FEMALE = 'w';

    private const GENDER_MALE = 'm';

    public function __construct(string $gender)
    {
        Assert::oneOf($gender, [self::GENDER_FEMALE, self::GENDER_MALE, self::GENDER_QUEER]);
        $this->gender = $gender;
    }

    public static function fromString(string $gender): self
    {
        return new self($gender);
    }

    public function gender(): string
    {
        return $this->gender;
    }

    public function isMale(): bool
    {
        return self::GENDER_MALE === $this->gender;
    }

    public function isFemale(): bool
    {
        return self::GENDER_FEMALE === $this->gender;
    }

    public function isQueer(): bool
    {
        return self::GENDER_QUEER === $this->gender;
    }

    public function equal(self $gender): bool
    {
        return $this->gender() === $gender->gender();
    }
}
