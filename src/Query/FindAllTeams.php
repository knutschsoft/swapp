<?php

declare(strict_types=1);

namespace App\Query;

use App\Entity\Teams;

interface FindAllTeams
{
    public function __invoke(): Teams;
}
