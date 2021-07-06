<?php
declare(strict_types=1);

namespace App\Dto\Walk;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['WalkCreateRequest', 'SecondGroup', 'ThirdGroup'])]
final class WalkCreateRequest
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 300)]
    public string $name;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Type(type: Team::class, groups: ['SecondGroup'])]
    public Team $team;

    #[Assert\NotNull]
    #[Assert\Length(min: 1, max: 2500)]
    public string $conceptOfDay;

    #[Assert\NotNull]
    #[Assert\Choice(choices: ['Sonne', 'Wolken', 'Regen', 'Schnee', 'Arschkalt'])]
    #[Assert\Type(type: 'string')]
    public string $weather;

    #[Assert\NotNull]
    #[Assert\Type(type: \DateTime::class, groups: ['SecondGroup'])]
    public \DateTime $startTime;

    /**
     * @var User[]
     *
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\NotNull,
     *     @Assert\Type(type="App\Entity\User", groups="{'SecondGroup'}")
     * })
     */
    #[Assert\NotNull]
    public array $walkTeamMembers;

    public bool $holidays;
}
