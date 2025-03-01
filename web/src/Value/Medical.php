<?php
declare(strict_types=1);

namespace App\Value;

use Symfony\Component\Serializer\Annotation\Groups;

final readonly class Medical
{
    public function __construct(private MedicalName $medicalName, private PeopleCount $peopleCount)
    {
    }

    public static function fromMedicalNameAndCount(MedicalName $medicalName, PeopleCount $peopleCount): self
    {
        return new self($medicalName, $peopleCount);
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getMedicalName(): MedicalName
    {
        return $this->medicalName;
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getPeopleCount(): PeopleCount
    {
        return $this->peopleCount;
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getFrontendLabel(): string
    {
        return \sprintf('%s', $this->medicalName->getName());
    }

    public function equalType(self $medical): bool
    {
        return $this->medicalName->equal($medical->getMedicalName());
    }
}
