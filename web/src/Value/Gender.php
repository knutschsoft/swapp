<?php
declare(strict_types=1);

namespace App\Value;

use Symfony\Component\Serializer\Annotation\Groups;
use Webmozart\Assert\Assert;

final class Gender
{
    public const GENDER_QUEER = 'x';

    public const GENDER_FEMALE = 'w';

    public const GENDER_MALE = 'm';

    public string $gender;

    public function __construct(string $gender)
    {
        Assert::oneOf($gender, [self::GENDER_FEMALE, self::GENDER_MALE, self::GENDER_QUEER]);
        $this->gender = $gender;
    }

    public static function fromString(string $gender): self
    {
        return new self($gender);
    }

    /**
     * @return string
     *
     * @Groups({"walk:read"})
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @return bool
     *
     * @Groups({"walk:read"})
     */
    public function isMale(): bool
    {
        return self::GENDER_MALE === $this->gender;
    }

    /**
     * @return bool
     *
     * @Groups({"walk:read"})
     */
    public function isFemale(): bool
    {
        return self::GENDER_FEMALE === $this->gender;
    }

    /**
     * @return bool
     *
     * @Groups({"walk:read"})
     */
    public function isQueer(): bool
    {
        return self::GENDER_QUEER === $this->gender;
    }

    public function equal(self $gender): bool
    {
        return $this->getGender() === $gender->getGender();
    }
}
