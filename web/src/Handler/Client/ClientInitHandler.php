<?php
declare(strict_types=1);

namespace App\Handler\Client;

use App\Dto\Client\ClientCreateRequest;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ClientInitHandler
{
    public function __construct(
        private readonly ClientRepository $clientRepository
    ) {
    }

    public function __invoke(ClientCreateRequest $request): Client
    {
        $client = Client::fromClientInitRequest($request);
        $this->clientRepository->save($client);

        return $client;
    }
}
