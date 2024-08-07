<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\Team;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
class InitialMembersConfigRequirements extends Compound
{
    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotNull(),
            new Assert\Choice(callback: [Team::class, 'getInitialMembersConfigChoices']),
        ];
    }
}
