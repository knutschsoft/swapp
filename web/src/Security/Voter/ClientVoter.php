<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ClientVoter extends Voter
{
    private const CLIENT_READ = 'CLIENT_READ';
    private const CLIENT_EDIT = 'CLIENT_EDIT';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return \in_array($attribute, [self::CLIENT_READ, self::CLIENT_EDIT], true)
            && $subject instanceof Client;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
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
            case self::CLIENT_READ:
                return $client->getUsers()->contains($user);
            case self::CLIENT_EDIT:
                return false;
        }

        return false;
    }
}
