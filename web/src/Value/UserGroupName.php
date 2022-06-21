<?php
declare(strict_types=1);

namespace App\Value;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource()]
final class UserGroupName
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
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

    public function equal(self $ageGroup): bool
    {
        return $this->getName() === $ageGroup->getName();
    }
}
