<?php
declare(strict_types=1);

namespace App\Dto\Walk;

use App\Entity\Walk;
use App\Validator\Constraints as AppAssert;
use Carbon\Carbon;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['WalkEpilogueRequest', 'SecondGroup'])]
final class WalkEpilogueRequest
{
    #[AppAssert\WalkRequirements]
    public Walk $walk;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 300)]
    public string $name;

    #[Assert\NotNull]
    #[Assert\Length(min: 0, max: 2500)]
    public string $commitments;

    /** @var string[] */
    #[AppAssert\ConceptOfDaySuggestionsRequirements]
    public array $conceptOfDay;

    #[Assert\NotNull]
    #[Assert\Length(min: 0, max: 2500)]
    public string $insights;

    #[Assert\NotNull]
    #[Assert\Length(min: 0, max: 2500)]
    public string $systemicAnswer;

    #[Assert\NotNull]
    #[Assert\Length(min: 0, max: 2500)]
    public string $walkReflection;

    #[Assert\NotNull]
    #[Assert\Range(min: 1, max: 5)]
    public int $rating;

    #[AppAssert\WeatherRequirements]
    public string $weather;

    #[AppAssert\DateTimeRequirements]
    public \DateTime $startTime;

    #[AppAssert\DateTimeRequirements]
    public \DateTime $endTime;

    #[Assert\NotNull]
    public bool $holidays;

    #[Assert\NotNull]
    public bool $isResubmission;

    #[Assert\IsTrue(message: 'walk.isStartTimeBeforeEndTime', groups: ['SecondGroup'])]
    public function isStartTimeBeforeEndTime(): bool
    {
        return $this->startTime <= $this->endTime;
    }

    #[Assert\IsTrue(message: 'walk.isStartTimeBeforeWayPointsVisitedAt', groups: ['SecondGroup'])]
    public function isStartTimeBeforeAllWayPoints(): bool
    {
        foreach ($this->walk->getWayPoints() as $wayPoint) {
            if ((new Carbon($wayPoint->getVisitedAt()))->endOfMinute() <= (new Carbon($this->startTime))->startOfMinute()) {
                return false;
            }
        }

        return true;
    }

    #[Assert\IsTrue(message: 'walk.isEndTimeAfterWayPointsVisitedAt', groups: ['SecondGroup'])]
    public function isEndTimeAfterWayPointsVisitedAt(): bool
    {
        foreach ($this->walk->getWayPoints() as $wayPoint) {
            if ($wayPoint->getVisitedAt() > $this->endTime) {
                return false;
            }
        }

        return true;
    }
}
