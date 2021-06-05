<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class NotAnEnabledUser extends Constraint
{
    public string $message = 'User "{{ value }}" is not enabled or does not exist at all.';

    public function validatedBy(): string
    {
        return NotAnEnabledUserValidator::class;
    }
}
