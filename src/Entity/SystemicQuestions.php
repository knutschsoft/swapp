<?php
declare(strict_types=1);

namespace App\Entity;

use VersatileCollections\StrictlyTypedCollectionInterface;
use VersatileCollections\StrictlyTypedCollectionInterfaceImplementationTrait;

class SystemicQuestions implements StrictlyTypedCollectionInterface
{
    use StrictlyTypedCollectionInterfaceImplementationTrait;

    /**
     * {@inheritdoc}
     */
    public function checkType($item): bool
    {
        return $item instanceof SystemicQuestion;
    }

    public function getType(): string
    {
        return SystemicQuestion::class;
    }
}
