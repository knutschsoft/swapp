<?php
declare(strict_types=1);

namespace App\Dto\User;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['UserEnableRequest', 'SecondGroup', 'ThirdGroup'])]
final class UserEnableRequest
{
    #[Assert\NotNull]
    #[Assert\Type(type: User::class, groups: ['SecondGroup'])]
    public User $user;

    #[Assert\IsTrue(groups: ['ThirdGroup'])]
    public function isUserDisabled(): bool
    {
        return !$this->user->isEnabled();
    }
}
