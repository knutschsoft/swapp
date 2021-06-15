<?php
declare(strict_types=1);

namespace App\Dto\User;

use App\Entity\Client;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['UserRegisterRequest', 'SecondGroup'])]
final class UserRegisterRequest
{
    #[AppAssert\UsernameRequirements]
    public string $username;

    #[AppAssert\EmailRequirements]
    public string $email;

    #[AppAssert\PasswordRequirements]
    public string $password;

    #[Assert\NotNull]
    #[Assert\Type(type: Client::class, groups: ['SecondGroup'])]
    public Client $client;
}
