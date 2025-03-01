<?php
declare(strict_types=1);

namespace App\Value;

use Symfony\Component\Serializer\Annotation\Groups;

final readonly class Counseling
{
    public function __construct(private CounselingName $counselingName, private PeopleCount $peopleCount)
    {
    }

    public static function fromCounselingNameAndCount(CounselingName $counselingName, PeopleCount $peopleCount): self
    {
        return new self($counselingName, $peopleCount);
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getCounselingName(): CounselingName
    {
        return $this->counselingName;
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getPeopleCount(): PeopleCount
    {
        return $this->peopleCount;
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getFrontendLabel(): string
    {
        return \sprintf('%s', $this->counselingName->getName());
    }

    public function equalType(self $counseling): bool
    {
        return $this->counselingName->equal($counseling->getCounselingName());
    }
}
