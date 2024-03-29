<?php
declare(strict_types=1);

namespace App\Entity\Fields;

use App\Value\AgeRange;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait AgeRangeField
{
    /** @var AgeRange[] */
    #[ORM\Column(type: 'json_document')]
    private array $ageRanges;

    /**
     * @return AgeRange[]
     */
    #[Groups(['team:read', 'walk:read'])]
    public function getAgeRanges(): array
    {
        return \array_values($this->ageRanges);
    }

    /**
     * @param AgeRange[] $ageRanges
     */
    public function setAgeRanges(array $ageRanges): void
    {
        \usort($ageRanges, static function (AgeRange $a, AgeRange $b) {
            if ($a->getRangeStart() < $b->getRangeStart()) {
                return -1;
            }
            if ($a->getRangeStart() === $b->getRangeStart() && $a->getRangeEnd() < $b->getRangeEnd()) {
                return -1;
            }

            return 1;
        });

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
