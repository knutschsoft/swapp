<?php
declare(strict_types=1);

namespace App\Handler\Client;

use App\Dto\Client\ClientChangeRequest;
use App\Entity\Client;
use App\Repository\ClientRepository;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final readonly class ClientChangeRequestHandler
{
    public function __construct(
        private ClientRepository $clientRepository,
        private FilesystemOperator $clientRatingImageStorage
    ) {
    }

    public function __invoke(ClientChangeRequest $request): Client
    {
        $client = $request->client;
        $client->updateByClientChangeRequest($request);

        $tmpFile = $request->getDecodedRatingImageData();
        $oldImageName = $client->getRatingImageName();
        if ($tmpFile && $request->ratingImageFileName) {
            $newImageName = \sprintf("%s_%s", \time(), $request->ratingImageFileName);
            $client->setRatingImageName($newImageName);

            $contents = \file_get_contents(\sprintf("%s%s%s", $tmpFile->getPath(), \DIRECTORY_SEPARATOR, $tmpFile->getFilename()));
            Assert::string($contents);
            $this->clientRatingImageStorage->write($newImageName, $contents);
            if ($newImageName !== $oldImageName && $oldImageName) {
                $this->clientRatingImageStorage->delete($oldImageName);
            }
        } elseif ($oldImageName) {
            $this->clientRatingImageStorage->delete($oldImageName);
            $client->unsetRatingImageName();
        }

        $this->clientRepository->save($client);

        return $client;
    }
}
