<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class IsUsernameUnique extends Constraint
{
    public string $message = 'user.username-is-not-unique';

    public function validatedBy(): string
    {
        return IsUsernameUniqueValidator::class;
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
