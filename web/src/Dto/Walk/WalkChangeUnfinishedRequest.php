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

    #[Assert\NotNull]
    #[Assert\Choice(choices: ['Sonne', 'Wolken', 'Regen', 'Schnee', 'Arschkalt'])]
    public string $weather;

    #[Assert\Sequentially([
        new Assert\NotNull(),
        new Assert\Type(type: \DateTime::class),
    ])]
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

    /** @var string[] */
    #[AppAssert\GuestNamesRequirements]
    public array $guestNames;
}
