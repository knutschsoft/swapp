<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class DoctrineORMWayPointRepository extends EntityRepository implements WayPointRepository
{
    public function findTrue()
    {
        return true;
    }
}