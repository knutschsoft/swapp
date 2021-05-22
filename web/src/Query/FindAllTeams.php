<?php
declare(strict_types=1);

namespace App\Query;

use App\Entity\Team;

interface FindAllTeams
{
    /**
     * @return Team[]
     */
    public function __invoke(): array;
}
