<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ClientNotOfCurrentUserValidator extends ConstraintValidator
{
    public function __construct(private readonly Security $security)
    {
    }

    /**
     * @param Client|null            $client
     * @param ClientNotOfCurrentUser $constraint
     */
    #[\Override]
    public function validate($client, Constraint $constraint): void
    {
        if (!$client instanceof Client) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        if ($client->getUsers()->contains($user)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $client->getName())
                ->addViolation();
        }
    }
}
