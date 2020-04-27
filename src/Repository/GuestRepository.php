<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Guest;

interface GuestRepository
{
    public function save(Guest $guest): void;
}
