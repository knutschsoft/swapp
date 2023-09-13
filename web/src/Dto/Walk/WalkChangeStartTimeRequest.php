<?php
declare(strict_types=1);

namespace App\Dto\Walk;

use App\Entity\Walk;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['WalkChangeStartTimeRequest', 'SecondGroup'])]
final class WalkChangeStartTimeRequest
{
    #[AppAssert\WalkRequirements]
    public Walk $walk;

    #[AppAssert\DateTimeRequirements]
    public \DateTime $startTime;

    #[Assert\IsTrue(message: 'walk.isStartTimeBeforeEndTime', groups: ['SecondGroup'])]
    public function isStartTimeBeforeEndTime(): bool
    {
        if (!$this->walk->getEndTime()) {
            return true;
        }

        return $this->startTime <= $this->walk->getEndTime();
    }

    #[Assert\IsTrue(message: 'walk.isStartTimeBeforeWayPointsVisitedAt', groups: ['SecondGroup'])]
    public function isStartTimeBeforeAllWayPoints(): bool
    {
        foreach ($this->walk->getWayPoints() as $wayPoint) {
            if ($wayPoint->getVisitedAt() < $this->startTime) {
                return false;
            }
        }

        return true;
    }
}
