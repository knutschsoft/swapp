<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\Tag;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
class TagColorRequirements extends Compound
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
            new Assert\Choice(choices: Tag::COLORS),
            new Assert\Type(['type' => 'string']),
        ];
    }
}
