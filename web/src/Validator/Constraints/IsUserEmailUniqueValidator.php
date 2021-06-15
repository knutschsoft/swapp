<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Dto\User\UserChangeRequest;
use App\Dto\User\UserCreateRequest;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsUserEmailUniqueValidator extends ConstraintValidator
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param UserCreateRequest|UserChangeRequest $request
     * @param Constraint                          $constraint
     */
    public function validate($request, Constraint $constraint): void
    {
        if (isset($request->user) && \strtolower($request->user->getEmail()) === \strtolower($request->email)) {
            return;
        }
        if ($this->userRepository->findOneBy(['email' => \strtolower($request->email)])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $request->email)
                ->atPath('email')
                ->addViolation();
        }
    }
}
