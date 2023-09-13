<?php
declare(strict_types=1);

namespace App\Dto\Walk;

use App\Entity\User;
use App\Entity\Walk;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['WalkChangeUnfinishedRequest', 'SecondGroup'])]
final class WalkChangeUnfinishedRequest
{
    #[AppAssert\WalkRequirements]
    public Walk $walk;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 300)]
    public string $name;

    /** @var string[] */
    #[AppAssert\ConceptOfDaySuggestionsRequirements]
    public array $conceptOfDay;

    #[AppAssert\WeatherRequirements]
    public string $weather;

    #[AppAssert\DateTimeRequirements]
    public \DateTime $startTime;

    #[Assert\NotNull]
    public bool $holidays;

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
