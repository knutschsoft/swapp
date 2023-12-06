<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
class ConceptOfDayRequirements extends Compound
{
    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\Sequentially([
                new Assert\NotNull(),
                new Assert\Count(min: 0, max: 100),
                new Assert\Type('array'),
                new Assert\All([
                    new Assert\NotBlank(),
                    new Assert\Length(min: 2, max: 100, normalizer: 'trim'),
                ]),
            ]),
        ];
    }
}
