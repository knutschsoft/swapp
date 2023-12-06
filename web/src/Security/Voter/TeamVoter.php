<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Webmozart\Assert\Assert;

class TeamVoter extends Voter
{
    final public const TEAM_CREATE = 'TEAM_CREATE';
    final public const TEAM_READ = 'TEAM_READ';
    final public const TEAM_EDIT = 'TEAM_EDIT';
    final public const TEAM_DELETE = 'TEAM_DELETE';

    public function __construct(private readonly Security $security)
    {
    }

    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        $supportsAttribute = \in_array($attribute, [self::TEAM_CREATE, self::TEAM_READ, self::TEAM_EDIT, self::TEAM_DELETE], true);
        $supportsSubject = $subject instanceof Team;

        return $supportsAttribute && $supportsSubject;
    }

    #[\Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
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

        Assert::isInstanceOf($subject, Team::class);
        $team = $subject;

        switch ($attribute) {
            case self::TEAM_READ:
                if ($team->getUsers()->contains($user)) {
                    return true;
                }
                if (!$this->security->isGranted('ROLE_ADMIN')) {
                    return false;
                }

                if ($team->getClient() !== $user->getClient()) {
                    return false;
                }

                return $user->getTeams()->contains($subject);
            case self::TEAM_CREATE:
            case self::TEAM_DELETE:
            case self::TEAM_EDIT:
                if (!$this->security->isGranted('ROLE_ADMIN')) {
                    return false;
                }

                return $team->getClient() === $user->getClient();
        }

        return false;
    }
}
