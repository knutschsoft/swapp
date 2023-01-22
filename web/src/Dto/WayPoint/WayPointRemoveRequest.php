<?php
declare(strict_types=1);

namespace App\Dto\WayPoint;

use App\Entity\WayPoint;
use App\Validator\Constraints as AppAssert;

final class WayPointRemoveRequest
{
    #[AppAssert\WayPointRequirements]
    public WayPoint $wayPoint;
}
