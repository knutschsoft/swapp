<?php
declare(strict_types=1);

namespace App\Dto\User;

use App\Entity\User;
use App\Validator\Constraints as AppAssert;
use App\Value\ConfirmationToken;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['PasswordChangeRequest', 'SecondGroup', 'ThirdGroup'])]
final class PasswordChangeRequest
{
    #[AppAssert\PasswordRequirements]
    public string $password;

    #[Assert\NotNull]
    #[Assert\Type(type: User::class, groups: ['SecondGroup'])]
    public User $user;

    #[Assert\NotNull]
    #[Assert\Type(type: ConfirmationToken::class, groups: ['SecondGroup'])]
    public ConfirmationToken $confirmationToken;

    #[Assert\IsTrue(groups: ['ThirdGroup'])]
    public function isConfirmationTokenValid(): bool
    {
        if ($this->confirmationToken->isEmpty()) {
            return false;
        }
        if ($this->user->getConfirmationToken()->isEmpty()) {
            return false;
        }

        return $this->user->getConfirmationToken()->equals($this->confirmationToken);
    }
}
