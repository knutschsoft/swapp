<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TeamVoter extends Voter
{
    public const TEAM_CREATE = 'TEAM_CREATE';
    public const TEAM_READ = 'TEAM_READ';
    public const TEAM_EDIT = 'TEAM_EDIT';
    public const TEAM_DELETE = 'TEAM_DELETE';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool
    {
        $supportsAttribute = \in_array($attribute, [self::TEAM_CREATE, self::TEAM_READ, self::TEAM_EDIT, self::TEAM_DELETE], true);
        $supportsSubject = $subject instanceof Team;

        return $supportsAttribute && $supportsSubject;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::TEAM_CREATE:
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                break;
            case self::TEAM_READ:
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }

                return $user->getTeams()->contains($subject);
            case self::TEAM_DELETE:
            case self::TEAM_EDIT:
                if (!$this->security->isGranted('ROLE_ADMIN')) {
                    return false;
                }

                return $user->getTeams()->contains($subject);
        }

        return false;
    }
}
