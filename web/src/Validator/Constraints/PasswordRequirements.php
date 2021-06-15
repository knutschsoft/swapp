<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
class PasswordRequirements extends Compound
{
    /**
     * @inheritDoc
     */
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(),
            new Assert\Type('string'),
            new Assert\Length(['min' => 7, 'max' => 40]),
            new Assert\NotCompromisedPassword(),
        ];
    }
}
