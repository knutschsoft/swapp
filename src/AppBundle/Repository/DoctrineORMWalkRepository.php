<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Walk;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;

class DoctrineORMWalkRepository extends EntityRepository implements WalkRepositoryInterface
{
    /**
     * @return Walk[]
     */
    public function findAll()
    {
        $queryBuilder = $this->createQueryBuilder('walk')
            ->select();
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @return Walk[]
     */
    public function findAllOrderBy($order, $sort = 'asc')
    {
        $queryBuilder = $this->createQueryBuilder('walk')
            ->select()
            ->orderBy($order, $sort);
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @return AbstractQuery
     */
    public function getFindAllQuery()
    {
        $queryBuilder = $this->createQueryBuilder('walk')
            ->select();
        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * @param int $id id of the walk entity
     *
     * @return Walk
     */
    public function findOneById($id)
    {

        return parent::findOneBy(['id' => $id]);
    }

    /**
     * @param Walk $walk
     */
    public function save(Walk $walk)
    {
        $this->_em->persist($walk);
        $this->_em->flush();
    }

    /**
     * @param Walk $walk
     */
    public function update(Walk $walk)
    {
        $this->_em->merge($walk);
        $this->_em->flush();
    }
}
