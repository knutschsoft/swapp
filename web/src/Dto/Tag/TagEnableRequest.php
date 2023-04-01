<?php
declare(strict_types=1);

namespace App\Dto\Tag;

use App\Entity\Tag;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['TagEnableRequest', 'SecondGroup', 'ThirdGroup'])]
final class TagEnableRequest
{
    #[Assert\NotNull]
    #[Assert\Type(type: Tag::class, groups: ['SecondGroup'])]
    public Tag $tag;

    #[Assert\IsTrue(groups: ['ThirdGroup'])]
    public function isTagDisabled(): bool
    {
        return !$this->tag->isEnabled();
    }
}
