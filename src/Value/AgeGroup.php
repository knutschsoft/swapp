<?php
declare(strict_types=1);

namespace App\Value;

final class AgeGroup
{
    /** @var AgeRange */
    public $ageRange;
    /** @var Gender */
    public $gender;
    /** @var PeopleCount */
    public $peopleCount;

    public function __construct(AgeRange $ageRange, Gender $gender, PeopleCount $peopleCount)
    {
        $this->ageRange = $ageRange;
        $this->gender = $gender;
        $this->peopleCount = $peopleCount;
    }

    public static function fromRangeGenderAndCount(AgeRange $range, Gender $gender, PeopleCount $peopleCount)
    {
        return new self($range, $gender, $peopleCount);
    }

    public function ageRange(): AgeRange
    {
        return $this->ageRange;
    }

    public function gender(): Gender
    {
        return $this->gender;
    }

    public function peopleCount(): PeopleCount
    {
        return $this->peopleCount;
    }

    public function equalType(AgeGroup $ageGroup): bool
    {
        return $this->ageRange->equal($ageGroup->ageRange()) && $this->gender->equal($ageGroup->gender());
    }
}
