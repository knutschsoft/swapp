<?php
namespace AppBundle\Repository;

use AppBundle\Entity\WayPoint;

interface WayPointRepository
{
    public function findTrue();

    public function findOneById($id);

    public function save(WayPoint $wayPoint);

    public function findAllFor($walkId);
}
