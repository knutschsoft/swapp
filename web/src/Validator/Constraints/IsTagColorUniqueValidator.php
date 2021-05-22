<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Repository\Exception\NotFoundException;
use App\Repository\TagRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsTagColorUniqueValidator extends ConstraintValidator
{
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @param string     $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        try {
            $this->tagRepository->findOneByColor($value);
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        } catch (NotFoundException) {
            // expected
        }
    }
}
