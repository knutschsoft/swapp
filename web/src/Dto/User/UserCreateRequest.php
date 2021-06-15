<?php
declare(strict_types=1);

namespace App\Dto\User;

use App\Entity\Client;
use App\Entity\User;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[AppAssert\IsUserEmailUnique(groups: ['SecondGroup'])]
#[AppAssert\IsUsernameUnique(groups: ['SecondGroup'])]
#[Assert\GroupSequence(['UserCreateRequest', 'SecondGroup'])]
final class UserCreateRequest
{
    #[AppAssert\UsernameRequirements]
    public string $username;

    #[AppAssert\EmailRequirements]
    public string $email;

    /**
     * @var string[]
     *
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\NotNull,
     *     @Assert\Type(type="string"),
     *     @Assert\Choice(choices=User::ROLES)
     * })
     */
    #[Assert\NotNull]
    public array $roles;

    #[Assert\NotNull]
    #[Assert\Type(type: Client::class, groups: ['SecondGroup'])]
    public Client $client;

    public function superAdminRightsNeeded(): bool
    {
        if (\in_array(User::ROLE_SUPER_ADMIN, $this->roles, true)) {
            return true;
        }

        return \in_array(User::ROLE_ALLOWED_TO_SWITCH, $this->roles, true);
    }
}
