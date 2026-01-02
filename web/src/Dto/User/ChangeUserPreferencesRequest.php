<?php
declare(strict_types=1);

namespace App\Dto\User;

use App\Entity\User;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(["ChangeUserPreferencesRequest", "SecondGroup"])]
final class ChangeUserPreferencesRequest
{
    #[AppAssert\UserRequirements]
    public User $user;

    /**
     * Partial preferences update (PATCH semantics).
     * Only the provided keys will be updated, others remain unchanged.
     *
     * @var array<string, mixed>
     */
    #[Assert\NotBlank]
    #[Assert\Type('array')]
    #[AppAssert\ValidUserPreferences]
    public array $preferences = [];
}
