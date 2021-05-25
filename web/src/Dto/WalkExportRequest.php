<?php
declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class WalkExportRequest
{
    public ?\DateTime $startTimeFrom = null;
    public ?\DateTime $startTimeTo = null;

    #[Assert\IsTrue()]
    public function getIsStartTimeFromIsBeforeStartTimeTo(): bool
    {
        if (!$this->startTimeFrom || !$this->startTimeTo) {
            return true;
        }

        return $this->startTimeFrom < $this->startTimeTo;
    }
}
