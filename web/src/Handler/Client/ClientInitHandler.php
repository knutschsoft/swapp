<?php
declare(strict_types=1);

namespace App\Handler\Client;

use App\Dto\Client\ClientCreateRequest;
use App\Entity\Client;
use App\Repository\ClientRepository;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final class ClientInitHandler
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly FilesystemOperator $clientRatingImageStorage
    ) {
    }

    public function __invoke(ClientCreateRequest $request): Client
    {
        $client = Client::fromClientInitRequest($request);
        $tmpFile = $request->getDecodedRatingImageData();
        $imageName = $client->getRatingImageName();
        if ($tmpFile && $imageName) {
            $contents = \file_get_contents(\sprintf("%s%s%s", $tmpFile->getPath(), \DIRECTORY_SEPARATOR, $tmpFile->getFilename()));
            Assert::string($contents);
            $this->clientRatingImageStorage->write($imageName, $contents);
        }

        $this->clientRepository->save($client);

        return $client;
    }
}
