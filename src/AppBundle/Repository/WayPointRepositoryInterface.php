<?php
namespace AppBundle\Repository;

use AppBundle\Entity\WayPoint;

interface WayPointRepositoryInterface
{
    /**
     * @return WayPoint
     */
    public function findOneById($id);

    /**
     * @param WayPoint $wayPoint
     */
    public function save(WayPoint $wayPoint);

    /**
     * @return WayPoint[]
     */
    public function findAll();

    /**
     * @return WayPoint[]
     */
    public function findAllFor($walkId);
}
