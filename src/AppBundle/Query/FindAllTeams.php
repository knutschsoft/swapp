<?php
declare(strict_types=1);

namespace AppBundle\Query;

use AppBundle\Entity\Teams;

interface FindAllTeams
{
    public function __invoke(): Teams;
}
