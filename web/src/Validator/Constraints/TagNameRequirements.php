<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
class TagNameRequirements extends Compound
{
    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(),
            new Assert\NotNull(),
            new Assert\Type('string'),
            new Assert\Length(min: 3, max: 100, normalizer: 'trim'),
        ];
    }
}
