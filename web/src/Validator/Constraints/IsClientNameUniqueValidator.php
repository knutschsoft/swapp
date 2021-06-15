<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Dto\Client\ClientChangeRequest;
use App\Dto\Client\ClientCreateRequest;
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
     * @param ClientCreateRequest|ClientChangeRequest $request
     * @param Constraint                              $constraint
     */
    public function validate($request, Constraint $constraint): void
    {
        if (isset($request->client) && \strtolower($request->client->getName()) === \strtolower($request->name)) {
            return;
        }
        try {
            $this->clientRepository->findOneByName($request->name);
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $request->name)
                ->atPath('name')
                ->addViolation();
        } catch (NotFoundException) {
            // expected
        }
    }
}
