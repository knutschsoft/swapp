<?php
declare(strict_types=1);

namespace App\Dto\Client;

use App\Entity\Client;
use App\Entity\Team;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(["ClientChangeRequest", "SecondGroup"])]
final class ClientChangeRequest
{
    use ClientRequest;

    #[Assert\NotNull]
    #[Assert\Type(type: Client::class, groups: ['SecondGroup'])]
    public Client $client;

    /**
     * @var User[]
     *
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\NotNull,
     *     @Assert\Type(type="App\Entity\User")
     * })
     */
    #[Assert\NotNull]
    public array $users;

    /**
     * @var Team[]
     *
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\NotNull,
     *     @Assert\Type(type="App\Entity\Team")
     * })
     */
    #[Assert\NotNull]
    public array $teams;
}
