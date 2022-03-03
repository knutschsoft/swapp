<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\Client;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
class ClientRequirements extends Compound
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
                new Assert\Type(Client::class),
            ]),
        ];
    }
}
