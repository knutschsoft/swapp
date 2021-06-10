<?php
declare(strict_types=1);

namespace App\Dto\Client;

use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(["ClientInitRequest", "SecondGroup"])]
final class ClientInitRequest
{
    use ClientRequest;
}
