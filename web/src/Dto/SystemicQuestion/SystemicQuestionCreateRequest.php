<?php
declare(strict_types=1);

namespace App\Dto\SystemicQuestion;

use App\Entity\Client;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class SystemicQuestionCreateRequest
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(['min' => 3, 'max' => 4000, 'normalizer' => 'trim'])]
    #[Assert\Type(['type' => 'string'])]
    public string $question;

    #[AppAssert\ClientRequirements]
    public Client $client;
}
