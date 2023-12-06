<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Dto\Client\ClientChangeRequest;
use App\Dto\Client\ClientCreateRequest;
use App\Repository\ClientRepository;
use App\Repository\Exception\NotFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsClientEmailUniqueValidator extends ConstraintValidator
{
    public function __construct(private readonly ClientRepository $clientRepository)
    {
    }

    /**
     * @param ClientCreateRequest|ClientChangeRequest $request
     * @param Constraint                              $constraint
     */
    #[\Override]
    public function validate($request, Constraint $constraint): void
    {
        if (isset($request->client) && \strtolower($request->client->getEmail()) === \strtolower($request->email)) {
            return;
        }
        try {
            $this->clientRepository->findOneByEmail($request->email);
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $request->email)
                ->atPath('email')
                ->addViolation();
        } catch (NotFoundException) {
            // expected
        }
    }
}
