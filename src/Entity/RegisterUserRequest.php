<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     shortName="User-Register",
 *     messenger=true,
 *     collectionOperations={
 *         "post"={
 *             "path"="/users/register",
 *             "status"=201,
 *             "openapi_context"={
 *                 "tags"={"User"},
 *                 "summary"="Register a new User."
 *             },
 *         }
 *     },
 *     itemOperations={}
 * )
 */
final class RegisterUserRequest
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Email()
     * @AppAssert\IsUserEmailUnique()
     */
    public string $email;
    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min="8", max="40")
     * @AppAssert\IsUsernameUnique()
     */
    public string $username;
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="8", max="40")
     * @Assert\NotCompromisedPassword(message="user.password.not_compromised")
     */
    public string $password;
}
