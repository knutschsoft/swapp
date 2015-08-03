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

    public function save(Walk $walk);

    public function update(Walk $walk);
}
