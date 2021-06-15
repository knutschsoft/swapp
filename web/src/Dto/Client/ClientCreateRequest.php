<?php
declare(strict_types=1);

namespace App\Dto\Client;

use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[AppAssert\IsClientNameUnique(groups: ['SecondGroup'])]
#[AppAssert\IsClientEmailUnique(groups: ['SecondGroup'])]
#[Assert\GroupSequence(["ClientCreateRequest", "SecondGroup"])]
final class ClientCreateRequest
{
    use ClientRequest;
}
