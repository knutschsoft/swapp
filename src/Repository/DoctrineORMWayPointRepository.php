<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\WayPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineORMWayPointRepository extends ServiceEntityRepository implements WayPointRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WayPoint::class);
    }

    /**
     * @return WayPoint[]
     */
    public function findAll(): array
    {
        $queryBuilder = $this->createQueryBuilder('way_point')
            ->select();
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param mixed $id
     *
     * @return WayPoint|null
     */
    public function findOneById($id): ?WayPoint
    {
        /** @var WayPoint|null $wayPoint */
        $wayPoint = parent::findOneBy(['id' => $id]);

        return $wayPoint;
    }

    public function save(WayPoint $wayPoint): void
    {
        $this->_em->persist($wayPoint);
        $this->_em->flush();
    }
}
