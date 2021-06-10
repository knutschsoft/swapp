<?php
declare(strict_types=1);

namespace App\Dto;

use App\Entity\Team;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(["WalkPrologueRequest", "SecondGroup"])]
final class WalkPrologueRequest
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type(type: Team::class, groups: ['SecondGroup'])]
    public Team $team;
}
