<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Walk;
use Doctrine\ORM\AbstractQuery;

interface WalkRepositoryInterface
{
    /**
     * @return Walk[]
     */
    public function findAll();

    /**
     * @return Walk[]
     */
    public function findAllOrderBy($order, $sort);

    /**
     * @return AbstractQuery
     */
    public function getFindAllQuery();

    /**
     * @param Walk $walk
     */
    public function save(Walk $walk);

    /**
     * @param Walk $walk
     */
    public function update(Walk $walk);
}
