<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\WayPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WayPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method WayPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method WayPoint[]    findAll()
 * @method WayPoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctrineORMWayPointRepository extends ServiceEntityRepository implements WayPointRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WayPoint::class);
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
