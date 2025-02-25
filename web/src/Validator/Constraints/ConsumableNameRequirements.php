<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Value\ConsumableName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
class ConsumableNameRequirements extends Compound
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
                new Assert\Type('array'),
                new Assert\All([
                    new Assert\NotNull(),
                    new Assert\Type(ConsumableName::class),
                ]),
            ]),
        ];
    }
}
