<?php
declare(strict_types=1);

namespace App\Entity\Fields;

use App\Value\ConsumableName;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait ConsumableNamesField
{
    /** @var ConsumableName[] */
    #[ORM\Column(type: 'json_document')]
    private array $consumableNames;

    /** @return ConsumableName[] */
    #[Groups(['team:read', 'walk:read'])]
    public function getConsumableNames(): array
    {
        return \array_values($this->consumableNames);
    }

    /** @param ConsumableName[] $consumableNames */
    public function setConsumableNames(array $consumableNames): void
    {
        $this->consumableNames = $consumableNames;
    }

    public function addConsumable(ConsumableName $consumableName): void
    {
        $this->consumableNames[] = $consumableName;
    }

    public function removeConsumable(ConsumableName $consumableName): void
    {
        foreach ($this->consumableNames as $key => $consumableToBeRemoved) {
            if ($consumableName->equal($consumableToBeRemoved)) {
                unset($this->consumableNames[$key]);
            }
        }
    }
}
