<?php
declare(strict_types=1);

namespace App\Value;

use Symfony\Component\Serializer\Annotation\Groups;

final readonly class AgeGroup
{
    public function __construct(private AgeRange $ageRange, private Gender $gender, private PeopleCount $peopleCount)
    {
    }

    public static function fromRangeGenderAndCount(AgeRange $range, Gender $gender, PeopleCount $peopleCount): self
    {
        return new self($range, $gender, $peopleCount);
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getAgeRange(): AgeRange
    {
        return $this->ageRange;
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getGender(): Gender
    {
        return $this->gender;
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getPeopleCount(): PeopleCount
    {
        return $this->peopleCount;
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getFrontendLabel(): string
    {
        return \sprintf('%d - %d %s', $this->ageRange->getRangeStart(), $this->ageRange->getRangeEnd(), $this->gender->getGender());
    }

    public function equalType(self $ageGroup): bool
    {
        return $this->ageRange->equal($ageGroup->getAgeRange()) && $this->gender->equal($ageGroup->getGender());
    }
}
