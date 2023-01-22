<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\WayPoint;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WayPointVoter extends Voter
{
    public const READ = 'WAY_POINT_READ';
    public const EDIT = 'WAY_POINT_EDIT';
    public const REMOVE = 'WAY_POINT_REMOVE';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::EDIT, self::READ, self::REMOVE], true)
            && $subject instanceof WayPoint;
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

        /** @var WayPoint $wayPoint */
        $wayPoint = $subject;

        switch ($attribute) {
            case self::REMOVE:
            case self::EDIT:
                if (!$this->security->isGranted(User::ROLE_ADMIN)) {
                    return false;
                }

                return $wayPoint->getWalk()->getClient()->getId() === $user->getClient()->getId();
            case self::READ:
                return $wayPoint->getWalk()->getClient()->getId() === $user->getClient()->getId();
        }

        return false;
    }
}
