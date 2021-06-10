<?php
declare(strict_types=1);

namespace App\Dto;

use App\Entity\Client;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(["WalkExportRequest", "SecondGroup"])]
class WalkExportRequest
{
    public ?\DateTime $startTimeFrom = null;
    public ?\DateTime $startTimeTo = null;

    #[Assert\NotNull]
    #[Assert\Type(type: Client::class, groups: ['SecondGroup'])]
    public Client $client;

    #[Assert\IsTrue()]
    public function getIsStartTimeFromIsBeforeStartTimeTo(): bool
    {
        if (!$this->startTimeFrom || !$this->startTimeTo) {
            return true;
        }

        return $this->startTimeFrom < $this->startTimeTo;
    }
}
