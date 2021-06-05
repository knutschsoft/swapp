<?php
declare(strict_types=1);

namespace App\Dto\User;

use App\Entity\User;
use App\Value\ConfirmationToken;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['IsConfirmationTokenValidRequest', 'SecondGroup', 'ThirdGroup'])]
final class IsConfirmationTokenValidRequest
{
    #[Assert\NotBlank]
    #[Assert\Type(type: User::class, groups: ['SecondGroup'])]
    public User $user;

    #[Assert\NotBlank]
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
