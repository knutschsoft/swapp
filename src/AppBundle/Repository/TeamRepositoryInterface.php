<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\Team;

interface TeamRepositoryInterface
{
    public function save(Team $team): void;
}
