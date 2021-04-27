<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

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
     * @param string     $email
     * @param Constraint $constraint
     */
    public function validate($email, Constraint $constraint): void
    {
        if ((bool) $this->userRepository->findOneBy(['email' => strtolower($email)])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $email)
                ->addViolation();
        };
    }
}
