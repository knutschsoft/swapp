<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Dto\WayPoint\WayPointChangeRequest;
use App\Dto\WayPoint\WayPointCreateRequest;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ContactsCountValidator extends ConstraintValidator
{
    /**
     * @param WayPointCreateRequest|WayPointChangeRequest $request
     * @param ContactsCount|Constraint                    $constraint
     */
    #[\Override]
    public function validate($request, ContactsCount|Constraint $constraint): void
    {
        if ($request instanceof WayPointCreateRequest) {
            $walk = $request->walk;
        } elseif ($request instanceof WayPointChangeRequest) {
            $walk = $request->wayPoint->getWalk();
        } else {
            throw new UnexpectedTypeException($constraint, ContactsCount::class);
        }

        if (!$walk->isWithContactsCount() && \is_int($request->contactsCount)) {
            $this->context
                ->buildViolation($constraint->messageShouldNotBeSet)
                ->atPath('contactsCount')
                ->addViolation();
        } elseif ($walk->isWithContactsCount() && \is_null($request->contactsCount)) {
            $this->context
                ->buildViolation($constraint->messageShouldBeSet)
                ->atPath('contactsCount')
                ->addViolation();
        }
    }
}
