<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Dto\User\UserChangeRequest;
use App\Dto\User\UserCreateRequest;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsUsernameUniqueValidator extends ConstraintValidator
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @param UserCreateRequest|UserChangeRequest $request
     * @param Constraint                          $constraint
     */
    #[\Override]
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
