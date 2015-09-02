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

    public function getTags()
    {
        $queryBuilder = $this->createQueryBuilder('tag')->select();
        $query = $queryBuilder->getQuery();
        $result = $query->getResult();

        $tagList = [];

        foreach ($result as $tag) {
            $tagList[] = $tag;
        }

        return $tagList;

        return $query->getResult();
    }

    public function updateTag(Tag $tag)
    {
        $this->_em->merge($tag);
        $this->_em->flush();
    }

    public function findAll()
    {
        $queryBuilder = $this->createQueryBuilder('tag')
            ->select();
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
}
