<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Walk;
use Doctrine\ORM\EntityRepository;

class DoctrineORMWalkRepository extends EntityRepository implements WalkRepository
{
    public function findTrue()
    {
        return true;
    }

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
     * @param int $id id of the walk entity
     *
     * @return Walk
     */
    public function findOneById($id)
    {

        return parent::findOneBy(['id' => $id]);
    }

    public function save(Walk $walk)
    {
        $this->_em->persist($walk);
        $this->_em->flush();
    }
}
