<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class DoctrineORMTagRepository extends EntityRepository implements TagRepository
{
    public function findTrue()
    {
        return true;
    }
}