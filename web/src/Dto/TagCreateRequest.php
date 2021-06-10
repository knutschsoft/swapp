<?php
declare(strict_types=1);

namespace App\Dto;

use App\Entity\Client;
use App\Entity\Tag;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(["TagCreateRequest", "SecondGroup", "ThirdGroup"])]
#[AppAssert\IsTagUnique(['groups' => 'ThirdGroup'])]
final class TagCreateRequest
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(['min' => 3, 'max' => 100, 'normalizer' => 'trim'])]
    #[Assert\Type(['type' => 'string'])]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(choices: Tag::COLORS)]
    #[Assert\Type(['type' => 'string'])]
    public string $color;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Type(type: Client::class, groups: ['SecondGroup'])]
    public Client $client;
}
