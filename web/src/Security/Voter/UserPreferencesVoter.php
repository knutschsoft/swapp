<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\UserPreferences;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**  @extends Voter<string, UserPreferences> */
class UserPreferencesVoter extends Voter
{
    final public const string READ = 'USER_PREFERENCES_READ';

    public function __construct(private readonly Security $security)
    {
    }

    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::READ], true)
            && $subject instanceof UserPreferences;
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

        /** @var UserPreferences $userPreferences */
        $userPreferences = $subject;

        switch ($attribute) {
            case self::READ:
                return $userPreferences->getUser()->equal($user);
        }

        return false;
    }
}
