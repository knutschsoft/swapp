<?php
declare(strict_types=1);

namespace App\Dto\Tag;

use App\Entity\Tag;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['TagEnableRequest', 'SecondGroup'])]
final class TagEnableRequest
{
    #[AppAssert\TagRequirements]
    public Tag $tag;

    #[Assert\IsTrue(groups: ['SecondGroup'])]
    public function isTagDisabled(): bool
    {
        return !$this->tag->isEnabled();
    }
}
