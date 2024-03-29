<?php
declare(strict_types=1);

namespace App\Dto\Team;

use App\Entity\Team;
use App\Entity\User;
use App\Validator\Constraints as AppAssert;
use App\Value\AgeRange;
use App\Value\UserGroupName;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['TeamChangeRequest', 'SecondGroup'])]
final class TeamChangeRequest
{
    #[AppAssert\TeamRequirements]
    public Team $team;

    #[AppAssert\TeamNameRequirements]
    public string $name;

    /** @var string[] */
    #[AppAssert\GuestNamesRequirements]
    public array $guestNames;

    /** @var string[] */
    #[AppAssert\LocationNamesRequirements]
    public array $locationNames;

    /** @var string[] */
    #[AppAssert\WalkNamesRequirements]
    public array $walkNames;

    /** @var string[] */
    #[AppAssert\ConceptOfDaySuggestionsRequirements]
    public array $conceptOfDaySuggestions;

    /** @var User[] */
    #[AppAssert\UsersRequirements]
    public array $users;

    #[AppAssert\IsWithAgeRangesRequirements]
    public bool $isWithAgeRanges;

    #[AppAssert\IsWithPeopleCountRequirements]
    public bool $isWithPeopleCount;

    /** @var AgeRange[] */
    #[AppAssert\AgeRangesRequirements]
    public array $ageRanges;

    #[AppAssert\IsWithGuestsRequirements]
    public bool $isWithGuests;

    #[AppAssert\IsWithSystemicQuestionRequirements]
    public bool $isWithSystemicQuestion;

    #[AppAssert\IsWithContactsCountRequirements]
    public bool $isWithContactsCount;

    #[AppAssert\IsWithUserGroupsRequirements]
    public bool $isWithUserGroups;

    /** @var UserGroupName[] */
    #[AppAssert\UserGroupNameRequirements]
    public array $userGroupNames;

    #[Assert\IsTrue(message: 'team.hasAgeRangesWhenIsWithAgeRangesIsTrue', groups: ['SecondGroup'])]
    public function hasAltersgruppen(): bool
    {
        if (!$this->isWithAgeRanges) {
            return true;
        }

        return \count($this->ageRanges) > 0;
    }

    #[Assert\IsTrue(message: 'team.hasPeopleCountWhenIsWithAgeRangesIsTrue', groups: ['SecondGroup'])]
    public function hasAnzahlPersonenVorOrt(): bool
    {
        if (!$this->isWithAgeRanges) {
            return true;
        }

        return $this->isWithPeopleCount;
    }
}
