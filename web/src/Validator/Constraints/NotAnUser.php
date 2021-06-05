<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class NotAnUser extends Constraint
{
    public string $message = 'User "{{ value }}" is not an user.';

    public function validatedBy(): string
    {
        return NotAnUserValidator::class;
    }
}
