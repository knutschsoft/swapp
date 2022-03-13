<?php
declare(strict_types=1);

namespace App\Dto\Client;

use App\Entity\Client;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[AppAssert\IsClientNameUnique(groups: ['SecondGroup'])]
#[AppAssert\IsClientEmailUnique(groups: ['SecondGroup'])]
#[Assert\GroupSequence(["ClientChangeRequest", "SecondGroup"])]
final class ClientChangeRequest
{
    use ClientRequest;

    #[AppAssert\ClientRequirements]
    public Client $client;
}
