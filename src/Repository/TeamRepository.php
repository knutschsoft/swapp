<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Team;

interface TeamRepository
{
    /**
     * @param int|string $id
     *
     * @return Team|null
     */
    public function findOneById($id): ?Team;

    public function save(Team $team): void;
}
