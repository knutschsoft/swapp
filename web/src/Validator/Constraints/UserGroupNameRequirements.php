<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Value\UserGroupName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
class UserGroupNameRequirements extends Compound
{
    /**
     * @inheritDoc
     */
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\Sequentially([
                new Assert\NotNull(),
                new Assert\Type('array'),
                new Assert\All([
                    new Assert\NotNull(),
                    new Assert\Type(UserGroupName::class),
                ]),
            ]),
        ];
    }
}
