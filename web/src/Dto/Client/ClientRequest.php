<?php
declare(strict_types=1);

namespace App\Dto\Client;

use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

trait ClientRequest
{
    #[Assert\NotNull]
    #[Assert\Length(['min' => 3, 'max' => 200])]
    #[Assert\Type(['type' => 'string'])]
    #[AppAssert\IsClientNameUnique(groups: ['SecondGroup'])]
    public string $name;

    #[Assert\NotNull]
    #[Assert\Length(['min' => 3, 'max' => 100])]
    #[Assert\Type(['type' => 'string'])]
    #[AppAssert\IsClientEmailUnique(groups: ['SecondGroup'])]
    public string $email;

    #[Assert\NotNull]
    #[Assert\Length(['min' => 0, 'max' => 10000])]
    #[Assert\Type(['type' => 'string'])]
    public string $description;
}
