<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
class ContactsCountRequirements extends Compound
{
    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\AtLeastOneOf([
                new Assert\IsNull(),
                new Assert\Sequentially([
                    new Assert\NotNull(),
                    new Assert\Type('int'),
                    new Assert\Range(min: 0, max: 40),
                ]),
            ]),
        ];
    }
}
