<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\WayPoint;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**  @extends Voter<string, WayPoint> */
class WayPointVoter extends Voter
{
    final public const string READ = 'WAY_POINT_READ';
    final public const string EDIT = 'WAY_POINT_EDIT';
    final public const string REMOVE = 'WAY_POINT_REMOVE';

    public function __construct(private readonly Security $security)
    {
    }

    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::EDIT, self::READ, self::REMOVE], true)
            && $subject instanceof WayPoint;
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

        /** @var WayPoint $wayPoint */
        $wayPoint = $subject;
        $walk = $wayPoint->getWalk();

        switch ($attribute) {
            case self::REMOVE:
            case self::EDIT:
                if (!$walk->getClient()->equal($user->getClient())) {
                    return false;
                }
                $walkCreator = $walk->getWalkCreator();
                if ($walkCreator && $walkCreator->equal($user)) {
                    return true;
                }

                return $this->security->isGranted(User::ROLE_ADMIN);
            case self::READ:
                return $walk->getClient()->equal($user->getClient());
        }

        return false;
    }
}
