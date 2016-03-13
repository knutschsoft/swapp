<?php
namespace AppBundle\Repository;

use AppBundle\Entity\WayPoint;
use Doctrine\ORM\EntityRepository;

class DoctrineORMWayPointRepository extends EntityRepository implements WayPointRepositoryInterface
{
    /**
     * @return WayPoint[]
     */
    public function findAll()
    {
        $queryBuilder = $this->createQueryBuilder('way_point')
            ->select();
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param int $id id of the wayPoint entity
     *
     * @return WayPoint
     */
    public function findOneById($id)
    {
        return parent::findOneBy(['id' => $id]);
    }

    /**
     * Finds all wayPoints for a walkId.
     *
     * @param $walkId
     *
     * @return WayPoint[]
     */
    public function findAllFor($walkId)
    {
        return parent::findBy(['walk' => $walkId]);
    }

    /**
     * @param WayPoint $wayPoint
     */
    public function save(WayPoint $wayPoint)
    {
        $this->_em->persist($wayPoint);
        $this->_em->flush();
    }
}
