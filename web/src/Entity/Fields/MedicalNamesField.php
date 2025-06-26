<?php
declare(strict_types=1);

namespace App\Entity\Fields;

use App\Value\MedicalName;
use Doctrine\ORM\Mapping as ORM;
use Dunglas\DoctrineJsonOdm\Type\JsonDocumentType;
use Symfony\Component\Serializer\Annotation\Groups;

trait MedicalNamesField
{
    /** @var MedicalName[] */
    #[ORM\Column(type: JsonDocumentType::NAME)]
    private array $medicalNames;

    /** @return MedicalName[] */
    #[Groups(['team:read', 'walk:read'])]
    public function getMedicalNames(): array
    {
        return \array_values($this->medicalNames);
    }

    /** @param MedicalName[] $medicalNames */
    public function setMedicalNames(array $medicalNames): void
    {
        $this->medicalNames = $medicalNames;
    }

    public function addMedical(MedicalName $medicalName): void
    {
        $this->medicalNames[] = $medicalName;
    }

    public function removeMedical(MedicalName $medicalName): void
    {
        foreach ($this->medicalNames as $key => $medicalToBeRemoved) {
            if ($medicalName->equal($medicalToBeRemoved)) {
                unset($this->medicalNames[$key]);
            }
        }
    }
}
