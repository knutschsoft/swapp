<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Walk;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class WalkVoter extends Voter
{
    public const READ = 'WALK_READ';
    public const EDIT = 'WALK_EDIT';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return \in_array($attribute, [self::EDIT, self::READ], true)
            && $subject instanceof Walk;
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

        /** @var Walk $walk */
        $walk = $subject;

        switch ($attribute) {
            case self::EDIT:
                if (!$this->security->isGranted(User::ROLE_ADMIN)) {
                    return false;
                }

                return $walk->getClient()->getId() === $user->getClient()->getId();
            case self::READ:
                return $walk->getClient()->getId() === $user->getClient()->getId();
        }

        return false;
    }
}
