<?php
declare(strict_types=1);

namespace App\Dto\User;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['UserDisableRequest', 'SecondGroup', 'ThirdGroup'])]
final class UserDisableRequest
{
    #[Assert\NotNull]
    #[Assert\Type(type: User::class, groups: ['SecondGroup'])]
    public User $user;

    #[Assert\IsTrue(groups: ['ThirdGroup'])]
    public function isUserEnabled(): bool
    {
        return $this->user->isEnabled();
    }
}
