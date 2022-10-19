<?php
declare(strict_types=1);

namespace App\Dto\Team;

use App\Entity\Team;
use App\Entity\User;
use App\Validator\Constraints as AppAssert;
use App\Value\AgeRange;
use App\Value\UserGroupName;

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

    /** @var User[] */
    #[AppAssert\UsersRequirements]
    public array $users;

    #[AppAssert\IsWithAgeRangesRequirements]
    public bool $isWithAgeRanges;

    /** @var AgeRange[] */
    #[AppAssert\AgeRangesRequirements]
    public array $ageRanges;

    #[AppAssert\IsWithGuestsRequirements]
    public bool $isWithGuests;

    #[AppAssert\IsWithContactsCountRequirements]
    public bool $isWithContactsCount;

    #[AppAssert\IsWithUserGroupsRequirements]
    public bool $isWithUserGroups;

    /** @var UserGroupName[] */
    #[AppAssert\UserGroupNameRequirements]
    public array $userGroupNames;
}
