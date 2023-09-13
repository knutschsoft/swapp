<?php
declare(strict_types=1);

namespace App\Dto\Walk;

use App\Entity\Team;
use App\Entity\User;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['WalkCreateRequest', 'SecondGroup'])]
final class WalkCreateRequest
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 300)]
    public string $name;

    #[AppAssert\TeamRequirements]
    public Team $team;

    /** @var string[] */
    #[AppAssert\ConceptOfDaySuggestionsRequirements]
    public array $conceptOfDay;

    #[AppAssert\WeatherRequirements]
    public string $weather;

    #[AppAssert\DateTimeRequirements]
    public \DateTime $startTime;

    /** @var User[] */
    #[AppAssert\UsersRequirements]
    public array $walkTeamMembers;

    #[AppAssert\UserRequirements]
    public User $walkCreator;

    public bool $holidays;

    /** @var string[] */
    #[AppAssert\GuestNamesRequirements]
    public array $guestNames;

    #[Assert\IsTrue(message: 'walk.isWalkCreatorInWalkMembers', groups: ['SecondGroup'])]
    public function isWalkCreatorInWalkMembers(): bool
    {
        foreach ($this->walkTeamMembers as $walkTeamMember) {
            if ($walkTeamMember->getId() === $this->walkCreator->getId()) {
                return true;
            }
        }

        return false;
    }
}
