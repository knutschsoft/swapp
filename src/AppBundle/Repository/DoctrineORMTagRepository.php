<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityRepository;

class DoctrineORMTagRepository extends EntityRepository implements TagRepository
{
    public function findTrue()
    {
        return true;
    }

    public function save(Tag $tag)
    {
        $this->_em->persist($tag);
        $this->_em->flush();
    }
}