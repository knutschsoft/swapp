<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class IsClientNameUnique extends Constraint
{
    public string $message = 'client.name_is_not_unique';

    #[\Override]
    public function validatedBy(): string
    {
        return IsClientNameUniqueValidator::class;
    }

    #[\Override]
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
