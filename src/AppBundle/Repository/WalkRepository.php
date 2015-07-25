<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Walk;

interface WalkRepository
{
    public function findTrue();

    /**
     * @return Walk[]
     */
    public function findAll();
}
