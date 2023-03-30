<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Walk;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WalkVoter extends Voter
{
    public const READ = 'WALK_READ';
    public const EDIT = 'WALK_EDIT';
    public const EDIT_START_TIME = 'WALK_EDIT_START_TIME';
    public const REMOVE = 'WALK_REMOVE';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::EDIT, self::EDIT_START_TIME, self::READ, self::REMOVE], true)
            && $subject instanceof Walk;
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

        /** @var Walk $walk */
        $walk = $subject;

        switch ($attribute) {
            case self::EDIT:
            case self::REMOVE:
                if (!$this->security->isGranted(User::ROLE_ADMIN)) {
                    return false;
                }

                return $walk->getClient()->getId() === $user->getClient()->getId();
            case self::EDIT_START_TIME:
            case self::READ:
                return $walk->getClient()->getId() === $user->getClient()->getId();
        }

        return false;
    }
}
