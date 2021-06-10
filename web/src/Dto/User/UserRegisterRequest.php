<?php
declare(strict_types=1);

namespace App\Dto\User;

use App\Entity\Client;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class UserRegisterRequest
{
    use PasswordRequest;

    #[Assert\NotNull]
    #[Assert\Length(['min' => 4, 'max' => 100])]
    #[Assert\Type(['type' => 'string'])]
    #[AppAssert\IsUserEmailUnique]
    public string $email;

    #[Assert\NotNull]
    #[Assert\Length(['min' => 4, 'max' => 100])]
    #[Assert\Type(['type' => 'string'])]
    #[AppAssert\IsUsernameUnique]
    public string $username;

    #[Assert\NotNull]
    #[Assert\Type(type: Client::class, groups: ['SecondGroup'])]
    public Client $client;
}
