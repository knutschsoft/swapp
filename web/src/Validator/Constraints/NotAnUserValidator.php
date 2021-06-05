<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Repository\Exception\NotFoundException;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotAnUserValidator extends ConstraintValidator
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string     $username
     * @param Constraint $constraint
     */
    public function validate($username, Constraint $constraint): void
    {
        try {
            $this->userRepository->findOneByEmailOrUsername($username);
        } catch (NotFoundException) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $username)
                ->addViolation();
        }
    }
}
