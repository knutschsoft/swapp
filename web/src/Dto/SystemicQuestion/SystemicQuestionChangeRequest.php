<?php
declare(strict_types=1);

namespace App\Dto\SystemicQuestion;

use App\Entity\Client;
use App\Entity\SystemicQuestion;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(["SystemicQuestionChangeRequest", "SecondGroup"])]
final class SystemicQuestionChangeRequest
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(['min' => 3, 'max' => 4000, 'normalizer' => 'trim'])]
    #[Assert\Type(['type' => 'string'])]
    public string $question;

    #[AppAssert\ClientRequirements]
    public Client $client;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Type(type: SystemicQuestion::class, groups: ['SecondGroup'])]
    public SystemicQuestion $systemicQuestion;
}
