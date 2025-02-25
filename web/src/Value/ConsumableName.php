<?php
declare(strict_types=1);

namespace App\Value;

use Symfony\Component\Serializer\Annotation\Groups;

final readonly class ConsumableName
{
    public function __construct(private string $name)
    {
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    #[Groups(['team:read', 'walk:read', 'wayPoint:read'])]
    public function getName(): string
    {
        return $this->name;
    }

    public function equal(self $consumableName): bool
    {
        return $this->getName() === $consumableName->getName();
    }
}
