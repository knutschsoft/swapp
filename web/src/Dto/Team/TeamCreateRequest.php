<?php
declare(strict_types=1);

namespace App\Dto\Team;

use App\Entity\Client;
use App\Entity\User;
use App\Value\AgeRange;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(["TeamCreateRequest", "SecondGroup"])]
final class TeamCreateRequest
{
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Type(type: Client::class, groups: ['SecondGroup'])]
    public Client $client;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 3, max: 100, normalizer: "trim")]
    #[Assert\Type(type: 'string')]
    public string $name;

    /**
     * @var string[]
     *
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\NotNull,
     *     @Assert\Length(min = 2, max = 300, normalizer="trim")
     * })
     */
    #[Assert\NotNull]
    #[Assert\Count(min: 0, max: 100)]
    #[Assert\Type(type: 'array')]
    public array $locationNames;

    /**
     * @var User[]
     *
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\NotNull,
     *     @Assert\Type(type="App\Entity\User")
     * })
     */
    #[Assert\NotNull]
    public array $users;

    /**
     * @var AgeRange[]
     *
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\NotNull,
     *     @Assert\Type(type="App\Value\AgeRange")
     * })
     */
    #[Assert\NotNull]
    public array $ageRanges;
}
