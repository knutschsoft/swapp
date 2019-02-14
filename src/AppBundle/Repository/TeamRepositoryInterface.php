<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\Team;

interface TeamRepositoryInterface
{
    /**
     * @param Team $team
     */
    public function save(Team $team);
}
