<?php
declare(strict_types=1);

namespace App\Dto\Walk;

use App\Entity\Walk;
use App\Validator\Constraints as AppAssert;

final class WalkRemoveRequest
{
    #[AppAssert\WalkRequirements]
    public Walk $walk;
}
