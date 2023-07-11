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
    public const READ = 'CLIENT_READ';
    public const EDIT = 'CLIENT_EDIT';
    public const CREATE = 'CLIENT_CREATE';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::READ, self::EDIT, self::CREATE], true)
            && $subject instanceof Client;
    }

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

        switch ($attribute) {
            case self::READ:
                return $client->getUsers()->contains($user);
            case self::EDIT:
            case self::CREATE:
                return false;
        }

        return false;
    }
}
