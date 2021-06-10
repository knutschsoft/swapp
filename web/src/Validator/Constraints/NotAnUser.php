<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class NotAnUser extends Constraint
{
    public string $message = 'user.not_found';

    public function validatedBy(): string
    {
        return NotAnUserValidator::class;
    }
}
