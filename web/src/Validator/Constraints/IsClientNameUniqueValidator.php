<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Repository\ClientRepository;
use App\Repository\Exception\NotFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsClientNameUniqueValidator extends ConstraintValidator
{
    private ClientRepository $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * @param string     $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        try {
            $this->clientRepository->findOneByName($value);
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        } catch (NotFoundException) {
            // expected
        }
    }
}
