<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class IsUserEmailUnique extends Constraint
{
    public string $message = 'user.email_is_not_unique';

    #[\Override]
    public function validatedBy(): string
    {
        return IsUserEmailUniqueValidator::class;
    }

    #[\Override]
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
