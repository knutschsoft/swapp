<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class IsClientEmailUnique extends Constraint
{
    public string $message = 'client.email_is_not_unique';

    public function validatedBy(): string
    {
        return IsClientEmailUniqueValidator::class;
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
