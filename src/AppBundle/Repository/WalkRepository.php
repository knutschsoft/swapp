<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Walk;
use Doctrine\ORM\AbstractQuery;

interface WalkRepository
{
    public function findTrue();

    /**
     * @return Walk[]
     */
    public function findAll();

    /**
     * @return AbstractQuery
     */
    public function getFindAllQuery();

    public function save(Walk $walk);

    public function update(Walk $walk);
}
