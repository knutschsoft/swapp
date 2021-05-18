<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsUsernameUnique extends Constraint
{
    public string $message = '{{ value }} is already used.';

    public function validatedBy(): string
    {
        return IsUsernameUniqueValidator::class;
    }
}
