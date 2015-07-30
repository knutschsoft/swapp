<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Team;

interface TeamRepository
{
    public function findTrue();

    public function save(Team $team);

    public function findAllFor($userId);
}
