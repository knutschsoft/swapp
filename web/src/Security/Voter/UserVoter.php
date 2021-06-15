<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const CREATE = 'USER_CREATE';
    public const READ = 'USER_READ';
    public const EDIT = 'USER_EDIT';
    public const DELETE = 'USER_DELETE';

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
        $supportsAttribute = \in_array($attribute, [self::CREATE, self::READ, self::EDIT, self::DELETE], true);
        $supportsSubject = $subject instanceof User;

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
