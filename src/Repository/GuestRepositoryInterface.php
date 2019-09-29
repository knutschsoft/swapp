<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Guest;

interface GuestRepositoryInterface
{
    /**
     * @param Guest $guest
     */
    public function save(Guest $guest): void;
}
