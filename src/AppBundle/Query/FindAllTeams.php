<?php

namespace AppBundle\Query;

use AppBundle\Entity\Teams;

interface FindAllTeams
{
    public function __invoke(): Teams;
}
