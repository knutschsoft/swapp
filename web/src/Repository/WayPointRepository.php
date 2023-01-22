<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\WayPoint;

interface WayPointRepository
{
    /** @param mixed $id */
    public function findOneById(mixed $id): ?WayPoint;

    public function save(WayPoint $wayPoint): void;

    public function remove(WayPoint $wayPoint): void;
}
