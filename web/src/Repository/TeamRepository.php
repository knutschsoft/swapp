<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Team;

interface TeamRepository
{
    /** @param int|string $id */
    public function findOneById(int|string $id): ?Team;

    public function save(Team $team): void;
}
