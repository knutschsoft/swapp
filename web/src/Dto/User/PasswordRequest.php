<?php
declare(strict_types=1);

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

trait PasswordRequest
{
    #[Assert\NotNull]
    #[Assert\NotCompromisedPassword(message: 'user.password_not_compromised')]
    #[Assert\Length(min: 7, max: 40)]
    public string $password;
}
