<?php
declare(strict_types=1);

namespace App\Entity\Fields;

use App\Value\CounselingName;
use Doctrine\ORM\Mapping as ORM;
use Dunglas\DoctrineJsonOdm\Type\JsonDocumentType;
use Symfony\Component\Serializer\Annotation\Groups;

trait CounselingNamesField
{
    /** @var CounselingName[] */
    #[ORM\Column(type: JsonDocumentType::NAME)]
    private array $counselingNames;

    /** @return CounselingName[] */
    #[Groups(['team:read', 'walk:read'])]
    public function getCounselingNames(): array
    {
        return \array_values($this->counselingNames);
    }

    /** @param CounselingName[] $counselingNames */
    public function setCounselingNames(array $counselingNames): void
    {
        $this->counselingNames = $counselingNames;
    }

    public function addCounseling(CounselingName $counselingName): void
    {
        $this->counselingNames[] = $counselingName;
    }

    public function removeCounseling(CounselingName $counselingName): void
    {
        foreach ($this->counselingNames as $key => $counselingToBeRemoved) {
            if ($counselingName->equal($counselingToBeRemoved)) {
                unset($this->counselingNames[$key]);
            }
        }
    }
}
