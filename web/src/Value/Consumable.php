<?php
declare(strict_types=1);

namespace App\Value;

use Symfony\Component\Serializer\Annotation\Groups;

final readonly class Consumable
{
    public function __construct(private ConsumableName $consumableName, private PeopleCount $peopleCount)
    {
    }

    public static function fromConsumableNameAndCount(ConsumableName $consumableName, PeopleCount $peopleCount): self
    {
        return new self($consumableName, $peopleCount);
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getConsumableName(): ConsumableName
    {
        return $this->consumableName;
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getPeopleCount(): PeopleCount
    {
        return $this->peopleCount;
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getFrontendLabel(): string
    {
        return \sprintf('%s', $this->consumableName->getName());
    }

    public function equalType(self $consumable): bool
    {
        return $this->consumableName->equal($consumable->getConsumableName());
    }
}
