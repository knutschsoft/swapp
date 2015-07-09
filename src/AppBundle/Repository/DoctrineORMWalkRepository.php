<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class DoctrineORMWalkRepository extends EntityRepository implements WalkRepository
{
    public function findTrue()
    {
        return true;
    }
}