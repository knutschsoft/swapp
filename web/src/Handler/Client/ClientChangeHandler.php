<?php
declare(strict_types=1);

namespace App\Handler\Client;

use App\Dto\Client\ClientChangeRequest;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ClientChangeHandler
{
    public function __construct(
        private readonly ClientRepository $clientRepository
    ) {
    }

    public function __invoke(ClientChangeRequest $request): Client
    {
        $client = $request->client;
        $client->updateByClientChangeRequest($request);
        $this->clientRepository->save($client);

        return $client;
    }
}
