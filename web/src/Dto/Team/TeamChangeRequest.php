<?php
declare(strict_types=1);

namespace App\Dto\Team;

use App\Entity\Team;
use App\Entity\User;
use App\Validator\Constraints as AppAssert;
use App\Value\AgeRange;

final class TeamChangeRequest
{
    #[AppAssert\TeamRequirements]
    public Team $team;

    #[AppAssert\TeamNameRequirements]
    public string $name;

    /** @var string[] */
    #[AppAssert\LocationNamesRequirements]
    public array $locationNames;

    /** @var User[] */
    #[AppAssert\UsersRequirements]
    public array $users;

    /** @var AgeRange[] */
    #[AppAssert\AgeRangesRequirements]
    public array $ageRanges;
}
