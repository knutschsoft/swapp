<?php
declare(strict_types=1);

namespace App\Handler\Client;

use App\Dto\Client\ClientChangeRequest;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ClientChangeHandler implements MessageHandlerInterface
{
    private ClientRepository $clientRepository;

    public function __construct(
        ClientRepository $clientRepository
    ) {
        $this->clientRepository = $clientRepository;
    }

    public function __invoke(ClientChangeRequest $request): Client
    {
        $client = $request->client;
        $client->updateByClientChangeRequest($request);
        $this->clientRepository->save($client);

        return $client;
    }
}
