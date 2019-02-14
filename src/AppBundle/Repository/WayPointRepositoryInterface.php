<?php
declare(strict_types=1);

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
}
