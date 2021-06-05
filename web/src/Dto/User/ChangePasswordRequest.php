<?php
declare(strict_types=1);

namespace App\Dto\User;

use App\Entity\User;
use App\Value\ConfirmationToken;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['ChangePasswordRequest', 'SecondGroup', 'ThirdGroup'])]
final class ChangePasswordRequest
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type(type: User::class, groups: ['SecondGroup'])]
    public User $user;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type(type: ConfirmationToken::class, groups: ['SecondGroup'])]
    public ConfirmationToken $confirmationToken;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\NotCompromisedPassword]
    #[Assert\Length(min: 7, max: 40)]
    public string $password;

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
