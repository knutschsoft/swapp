<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\Walk;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
class WalkRequirements extends Compound
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
                new Assert\NotBlank(),
                new Assert\Type(Walk::class),
            ]),
        ];
    }
}
