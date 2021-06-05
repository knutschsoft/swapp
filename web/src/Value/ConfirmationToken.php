<?php
declare(strict_types=1);

namespace App\Value;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/** @ORM\Embeddable() */
class ConfirmationToken
{
    /** @ORM\Column(type="string", length=180, name="confirmation_token") */
    private string $token;

    private const EMPTY_TOKEN = '01234567890123456789012345678912';

    public function __construct(string $token)
    {
        Assert::length($token, 32);
        $this->token = $token;
    }

    public static function fromString(string $token): self
    {
        return new self($token);
    }

    public static function create(): self
    {
        return self::fromString(\md5((string) \time(), false));
    }

    public static function createEmpty(): self
    {
        return self::fromString(self::EMPTY_TOKEN);
    }

    public function getToken(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->token;
    }

    public function __toString(): string
    {
        return $this->token;
    }

    public function isEmpty(): bool
    {
        return self::EMPTY_TOKEN === $this->getToken();
    }

    public function equals(self $token): bool
    {
        return $token->getToken() === $this->getToken();
    }
}
