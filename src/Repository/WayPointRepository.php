<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\WayPoint;

interface WayPointRepository
{
    /**
     * @param mixed $id
     *
     * @return WayPoint|null
     */
    public function findOneById($id): ?WayPoint;

    public function save(WayPoint $wayPoint): void;

    /**
     * @return WayPoint[]
     */
    public function findAll(): array;
}
