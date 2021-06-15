<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Dto\User\UserChangeRequest;
use App\Dto\User\UserRegisterRequest;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsUsernameUniqueValidator extends ConstraintValidator
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param UserRegisterRequest|UserChangeRequest $request
     * @param Constraint                            $constraint
     */
    public function validate($request, Constraint $constraint): void
    {
        if (isset($request->user) && \strtolower($request->user->getUsername()) === \strtolower($request->username)) {
            return;
        }
        if ($this->userRepository->findOneBy(['username' => \strtolower($request->username)])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $request->username)
                ->atPath('username')
                ->addViolation();
        }
    }
}
