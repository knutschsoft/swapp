<?php
declare(strict_types=1);

namespace App\Dto\Walk;

use App\Entity\Walk;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['WalkEpilogueRequest', 'SecondGroup'])]
final class WalkEpilogueRequest
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type(type: Walk::class, groups: ['SecondGroup'])]
    public Walk $walk;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 300)]
    public string $name;

    #[Assert\NotNull]
    #[Assert\Length(min: 0, max: 2500)]
    public string $commitments;

    #[Assert\NotNull]
    #[Assert\Length(min: 1, max: 2500)]
    public string $conceptOfDay;

    #[Assert\NotNull]
    #[Assert\Length(min: 0, max: 2500)]
    public string $insights;

    #[Assert\NotNull]
    #[Assert\Length(min: 0, max: 2500)]
    public string $systemicAnswer;

    #[Assert\NotNull]
    #[Assert\Length(min: 0, max: 2500)]
    public string $walkReflection;

    #[Assert\NotNull]
    #[Assert\Range(min: 1, max: 5)]
    public int $rating;

    #[Assert\NotNull]
    #[Assert\Choice(choices: ['Sonne', 'Wolken', 'Regen', 'Schnee', 'Arschkalt'])]
    public string $weather;

    #[Assert\Sequentially([
        new Assert\NotNull(),
        new Assert\Type(type: \DateTime::class),
    ])]
    public \DateTime $startTime;

    #[Assert\Sequentially([
        new Assert\NotNull(),
        new Assert\Type(type: \DateTime::class),
    ])]
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
}
