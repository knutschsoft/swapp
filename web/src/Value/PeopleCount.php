<?php
declare(strict_types=1);

namespace App\Value;

use Symfony\Component\Serializer\Annotation\Groups;
use Webmozart\Assert\Assert;

final class PeopleCount
{
    private int $count;

    public function __construct(int $count)
    {
        $this->validateCount($count);
        $this->count = $count;
    }

    public static function fromInt(int $count): self
    {
        return new self($count);
    }

    public static function none(): self
    {
        return new self(0);
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getCount(): int
    {
        return $this->count;
    }

    private function validateCount(int $count): void
    {
        Assert::greaterThanEq($count, 0);
    }
}
