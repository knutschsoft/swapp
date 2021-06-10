<?php
declare(strict_types=1);

namespace App\Handler\Client;

use App\Dto\Client\ClientInitRequest;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ClientInitHandler implements MessageHandlerInterface
{
    private ClientRepository $clientRepository;

    public function __construct(
        ClientRepository $clientRepository
    ) {
        $this->clientRepository = $clientRepository;
    }

    public function __invoke(ClientInitRequest $request): Client
    {
        $client = Client::fromClientInitRequest($request);
        $this->clientRepository->save($client);

        return $client;
    }
}
