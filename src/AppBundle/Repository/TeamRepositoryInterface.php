<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Team;

interface TeamRepositoryInterface
{
    /**
     * @param Team $team
     */
    public function save(Team $team);

    /**
     * @param int $userId
     *
     * @return Team[]
     */
    public function findAllFor($userId);
}
