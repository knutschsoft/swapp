<?php
declare(strict_types=1);

namespace App\Dto\Tag;

use App\Entity\Client;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(["TagCreateRequest", "SecondGroup"])]
#[AppAssert\IsTagUnique(['groups' => 'SecondGroup'])]
final class TagCreateRequest
{
    #[AppAssert\TagNameRequirements]
    public string $name;

    #[AppAssert\TagColorRequirements]
    public string $color;

    #[AppAssert\ClientRequirements]
    public Client $client;
}
