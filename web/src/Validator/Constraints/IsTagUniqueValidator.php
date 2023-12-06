<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Dto\Tag\TagCreateRequest;
use App\Repository\Exception\NotFoundException;
use App\Repository\TagRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsTagUniqueValidator extends ConstraintValidator
{
    public function __construct(private readonly TagRepository $tagRepository)
    {
    }

    /**
     * @param TagCreateRequest $value
     * @param Constraint       $constraint
     */
    #[\Override]
    public function validate($value, Constraint $constraint): void
    {
        $tagRequest = $value;

        try {
            $this->tagRepository->findOneByColorAndNameAndClient($tagRequest->color, $tagRequest->name, $value->client);
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ name }}', $value->name)
                ->setParameter('{{ color }}', $value->color)
                ->setParameter('{{ client }}', $value->client->getName())
                ->addViolation();
        } catch (NotFoundException) {
            // expected
        }
    }
}
