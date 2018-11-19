<?php
declare(strict_types=1);

namespace AppBundle\Value;

use Webmozart\Assert\Assert;

final class Gender
{
    /** @var string */
    public $gender;

    const GENDER_QUEER = 'x';

    const GENDER_FEMALE = 'w';

    const GENDER_MALE = 'm';

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
        return $this->gender === self::GENDER_MALE;
    }

    public function isFemale(): bool
    {
        return $this->gender === self::GENDER_FEMALE;
    }

    public function isQueer(): bool
    {
        return $this->gender === self::GENDER_QUEER;
    }

    public function equal(Gender $gender): bool
    {
        return $this->gender() === $gender->gender();
    }
}
