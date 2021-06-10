<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class IsTagUnique extends Constraint
{
    public string $message = 'tag.is_not_unique';

    public function validatedBy(): string
    {
        return IsTagUniqueValidator::class;
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
