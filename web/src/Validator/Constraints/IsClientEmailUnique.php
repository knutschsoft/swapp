<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class IsClientEmailUnique extends Constraint
{
    public string $message = 'client.email_is_not_unique';

    public function validatedBy(): string
    {
        return IsClientEmailUnique::class;
    }
}
