<?php
declare(strict_types=1);

namespace App\Dto\User;

use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class RequestPasswordResetRequest
{
    #[Assert\NotBlank]
    #[AppAssert\NotAnUser(['message' => 'user.not_found'])]
    #[AppAssert\NotAnEnabledUser(['message' => 'user.not_found_or_disabled'])]
    public string $username;

    /**
     * Used as honeypot. No need to validate.
     * Handler will silently fail if it is not an empty string.
     */
    public string $email = '';
}
