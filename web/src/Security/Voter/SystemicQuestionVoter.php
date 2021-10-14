<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\SystemicQuestion;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class SystemicQuestionVoter extends Voter
{
    public const READ = 'SYSTEMIC_QUESTION_READ';
    public const EDIT = 'SYSTEMIC_QUESTION_EDIT';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return \in_array($attribute, [self::READ, self::EDIT], true)
            && $subject instanceof SystemicQuestion;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted(User::ROLE_SUPER_ADMIN)) {
            return true;
        }

        /** @var SystemicQuestion $systemicQuestion */
        $systemicQuestion = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::READ:
                return $systemicQuestion->getClient() === $user->getClient();
            case self::EDIT:
                if (!$this->security->isGranted(User::ROLE_ADMIN)) {
                    return false;
                }

                return $systemicQuestion->getClient() === $user->getClient();
        }

        return false;
    }
}
