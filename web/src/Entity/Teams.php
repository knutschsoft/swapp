<?php
declare(strict_types=1);

namespace App\Entity;

use VersatileCollections\StrictlyTypedCollectionInterface;
use VersatileCollections\StrictlyTypedCollectionInterfaceImplementationTrait;

/**
 * @implements \IteratorAggregate<int, Team>
 */
class Teams implements StrictlyTypedCollectionInterface
{
    use StrictlyTypedCollectionInterfaceImplementationTrait;

    /**
     * {@inheritdoc}
     */
    public function checkType($item): bool
    {
        return $item instanceof Team;
    }

    public function getType(): string
    {
        return Team::class;
    }
}
