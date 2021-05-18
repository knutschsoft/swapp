<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute]
class IsTagNameUnique extends Constraint
{
    public string $message = 'tag.name-is-not-unique';

    public function validatedBy(): string
    {
        return IsTagNameUniqueValidator::class;
    }
}
