<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class IsClientNameUnique extends Constraint
{
    public string $message = 'client.name-is-not-unique';

    public function validatedBy(): string
    {
        return IsClientNameUniqueValidator::class;
    }
}
