<?php

namespace AppBundle\Entity;

use VersatileCollections\StrictlyTypedCollectionInterface;
use VersatileCollections\StrictlyTypedCollectionInterfaceImplementationTrait;

class Teams implements StrictlyTypedCollectionInterface
{
    use StrictlyTypedCollectionInterfaceImplementationTrait;

    public function checkType($item): bool
    {
        return ($item instanceof Team);
    }

    public function getType(): string
    {
        return Team::class;
    }
}