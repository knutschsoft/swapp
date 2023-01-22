<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\WayPoint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
class WayPointRequirements extends Compound
{
    /**
     * @inheritDoc
     */
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\Sequentially([
                new Assert\NotNull(),
                new Assert\NotBlank(),
                new Assert\Type(WayPoint::class),
            ]),
        ];
    }
}
