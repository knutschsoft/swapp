<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute]
class IsTagColorUnique extends Constraint
{
    public string $message = 'tag.color-is-not-unique';

    public function validatedBy(): string
    {
        return IsTagColorUniqueValidator::class;
    }
}
