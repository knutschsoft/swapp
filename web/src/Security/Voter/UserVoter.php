<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    final public const CREATE = 'USER_CREATE';
    final public const READ = 'USER_READ';
    final public const EDIT = 'USER_EDIT';
    final public const DELETE = 'USER_DELETE';

    public function __construct(private readonly Security $security)
    {
    }

    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        $supportsAttribute = \in_array($attribute, [self::CREATE, self::READ, self::EDIT, self::DELETE], true);
        $supportsSubject = $subject instanceof User;

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

        /** @var User $subjectUser */
        $subjectUser = $subject;

        switch ($attribute) {
            case self::READ:
                return $user->getClient()->getId() === $subjectUser->getClient()->getId();
            case self::CREATE:
            case self::DELETE:
            case self::EDIT:
                if (!$this->security->isGranted('ROLE_ADMIN')) {
                    return false;
                }

                return $user->getClient()->getId() === $subjectUser->getClient()->getId();
        }

        return false;
    }
}
