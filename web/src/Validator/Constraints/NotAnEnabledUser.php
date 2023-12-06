<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class NotAnEnabledUser extends Constraint
{
    public string $message = 'user.not_found_or_disabled';

    #[\Override]
    public function validatedBy(): string
    {
        return NotAnEnabledUserValidator::class;
    }
}
