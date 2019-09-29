<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Team;

interface TeamRepositoryInterface
{
    public function save(Team $team): void;
}
