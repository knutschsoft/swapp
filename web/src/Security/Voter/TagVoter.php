<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Tag;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class TagVoter extends Voter
{
    private const TAG_READ = 'TAG_READ';
    private const TAG_EDIT = 'TAG_EDIT';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return \in_array($attribute, [self::TAG_EDIT, self::TAG_READ], true)
            && $subject instanceof Tag;
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

        /** @var Tag $tag */
        $tag = $subject;

        switch ($attribute) {
            case self::TAG_EDIT:
                if (!$this->security->isGranted(User::ROLE_ADMIN)) {
                    return false;
                }

                return $tag->getClient()->getUsers()->contains($user);
            case self::TAG_READ:
                return $tag->getClient()->getUsers()->contains($user);
        }

        return false;
    }
}
