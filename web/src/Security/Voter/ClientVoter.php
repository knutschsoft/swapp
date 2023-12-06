<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ClientVoter extends Voter
{
    final public const READ = 'CLIENT_READ';
    final public const EDIT = 'CLIENT_EDIT';
    final public const CREATE = 'CLIENT_CREATE';

    public function __construct(private readonly Security $security)
    {
    }

    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::READ, self::EDIT], true)
            && $subject instanceof Client
            || \in_array($attribute, [self::CREATE], true);
    }

    #[\Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted(User::ROLE_SUPER_ADMIN)) {
            return true;
        }

        /** @var Client $client */
        $client = $subject;

        return match ($attribute) {
            self::READ => $client->getUsers()->contains($user),
            self::EDIT, self::CREATE => false,
            default => false,
        };
    }
}
