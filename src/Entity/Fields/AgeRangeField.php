<?php
declare(strict_types=1);

namespace App\Entity\Fields;

use App\Value\AgeRange;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait AgeRangeField
{
    /**
     * @ORM\Column(type="json_document", options={"jsonb": true})
     *
     * @var \App\Value\AgeRange[]
     */
    private array $ageRanges;

    /**
     * @return AgeRange[]
     *
     * @Groups({"team:read"})
     */
    public function getAgeRanges(): array
    {
        return \array_values($this->ageRanges);
    }

    public function setAgeRanges(array $ageRanges): void
    {
        $this->ageRanges = $ageRanges;
    }

    public function addAgeRange(AgeRange $ageRange): void
    {
        $this->ageRanges[] = $ageRange;
    }

    public function removeAgeRange(AgeRange $ageRange): void
    {
        foreach ($this->ageRanges as $key => $ageRangeToBeRemoved) {
            if ($ageRange->equal($ageRangeToBeRemoved)) {
                unset($this->ageRanges[$key]);
            }
        }
    }
}
