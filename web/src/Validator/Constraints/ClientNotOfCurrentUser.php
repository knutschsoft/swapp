<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ClientNotOfCurrentUser extends Constraint
{
    public string $message = 'client.remove.not_own_client';

    #[\Override]
    public function validatedBy(): string
    {
        return ClientNotOfCurrentUserValidator::class;
    }
}
