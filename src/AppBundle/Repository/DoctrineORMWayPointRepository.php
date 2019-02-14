<?php
declare(strict_types=1);

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
     * @param WayPoint $wayPoint
     */
    public function save(WayPoint $wayPoint)
    {
        $this->_em->persist($wayPoint);
        $this->_em->flush();
    }
}
