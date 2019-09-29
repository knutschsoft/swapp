<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\Guest;

interface GuestRepositoryInterface
{
    /**
     * @param Guest $guest
     */
    public function save(Guest $guest): void;
}
