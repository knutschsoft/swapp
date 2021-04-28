<?php
declare(strict_types=1);

namespace App\Value;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource()
 */
final class AgeGroup
{
    public AgeRange $ageRange;

    public Gender $gender;

    public PeopleCount $peopleCount;

    public function __construct(AgeRange $ageRange, Gender $gender, PeopleCount $peopleCount)
    {
        $this->ageRange = $ageRange;
        $this->gender = $gender;
        $this->peopleCount = $peopleCount;
    }

    public static function fromRangeGenderAndCount(AgeRange $range, Gender $gender, PeopleCount $peopleCount): self
    {
        return new self($range, $gender, $peopleCount);
    }

    /**
     * @Groups({"walk:read"})
     */
    public function getAgeRange(): AgeRange
    {
        return $this->ageRange;
    }

    /**
     * @Groups({"walk:read"})
     */
    public function getGender(): Gender
    {
        return $this->gender;
    }

    /**
     * @Groups({"walk:read"})
     */
    public function getPeopleCount(): PeopleCount
    {
        return $this->peopleCount;
    }

    public function equalType(self $ageGroup): bool
    {
        return $this->ageRange->equal($ageGroup->getAgeRange()) && $this->gender->equal($ageGroup->getGender());
    }
}
