<?php
namespace AppBundle\Repository;

use AppBundle\Entity\WayPoint;

interface WayPointRepository
{
    public function findTrue();

    /**
     * @return WayPoint
     */
    public function findOneById($id);

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
