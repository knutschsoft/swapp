<?php
declare(strict_types=1);

namespace App\Dto\SystemicQuestion;

use App\Entity\SystemicQuestion;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(["SystemicQuestionDisableRequest", "SecondGroup"])]
final class SystemicQuestionDisableRequest
{
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Type(type: SystemicQuestion::class, groups: ['SecondGroup'])]
    public SystemicQuestion $systemicQuestion;
}
