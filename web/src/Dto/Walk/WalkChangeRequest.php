<?php
declare(strict_types=1);

namespace App\Dto\Walk;

use App\Entity\User;
use App\Entity\Walk;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['WalkChangeRequest', 'SecondGroup'])]
final class WalkChangeRequest
{
    #[AppAssert\WalkRequirements]
    public Walk $walk;

    #[AppAssert\WalkNameRequirements]
    public string $name;

    #[AppAssert\TextareaRequirements]
    public string $commitments;

    /** @var string[] */
    #[AppAssert\ConceptOfDaySuggestionsRequirements]
    public array $conceptOfDay;

    #[AppAssert\TextareaRequirements]
    public string $insights;

    #[AppAssert\TextareaRequirements]
    public string $systemicAnswer;

    #[AppAssert\TextareaRequirements]
    public string $walkReflection;

    #[AppAssert\RatingRequirements]
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
            if ($wayPoint->getVisitedAt() < $this->startTime) {
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

    /** @var User[] */
    #[AppAssert\UsersRequirements]
    public array $walkTeamMembers;

    #[AppAssert\UserOptionalRequirements]
    public ?User $walkCreator;

    /** @var string[] */
    #[AppAssert\GuestNamesRequirements]
    public array $guestNames;

    #[Assert\IsTrue(message: 'walk.isWalkCreatorInWalkMembers', groups: ['SecondGroup'])]
    public function isWalkCreatorInWalkMembers(): bool
    {
        if ($this->walk->getWalkCreator() && !$this->walkCreator) {
            return false;
        }
        if (!$this->walkCreator) {
            return true;
        }

        foreach ($this->walkTeamMembers as $walkTeamMember) {
            if ($walkTeamMember->getId() === $this->walkCreator->getId()) {
                return true;
            }
        }

        return false;
    }
}
