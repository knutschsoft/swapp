<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class UsernameConstraint extends Constraint
{
    public string $message = 'Der Nutzername "{{ value }}" ist ungültig. Erlaubt sind nur Kleinbuchstaben, Punkt und Bindestrich, jedoch nicht am Anfang oder Ende.';
}
