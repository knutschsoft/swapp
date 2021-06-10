<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class IsUsernameUnique extends Constraint
{
    public string $message = 'user.username-is-not-unique';

    public function validatedBy(): string
    {
        return IsUsernameUniqueValidator::class;
    }
}
