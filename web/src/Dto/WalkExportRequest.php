<?php
declare(strict_types=1);

namespace App\Dto;

use App\Entity\Client;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class WalkExportRequest
{
    public ?\DateTime $startTimeFrom = null;
    public ?\DateTime $startTimeTo = null;

    #[AppAssert\ClientRequirements]
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
