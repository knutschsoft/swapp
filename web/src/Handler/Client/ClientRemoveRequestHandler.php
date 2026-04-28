<?php
declare(strict_types=1);

namespace App\Handler\Client;

use App\Dto\Client\ClientRemoveRequest;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ClientRemoveRequestHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FilesystemOperator $wayPointImageStorage,
        private FilesystemOperator $clientRatingImageStorage
    ) {
    }

    public function __invoke(ClientRemoveRequest $request): void
    {
        $client = $request->client;

        // Reihenfolge ist wichtig wegen FK-Constraints:
        // 1) WayPoints (inkl. Bilddateien) - referenzieren Walks und Tags (M2M)
        // 2) Walks                        - referenzieren Client + walkCreator/Users (M2M)
        // 3) Tags                         - referenzieren Client; M2M zu WayPoints jetzt leer
        // 4) Users                        - referenzieren Client; UserPreferences via DB-CASCADE
        // 5) Teams                        - referenzieren Client; M2M zu Users jetzt leer
        // 6) SystemicQuestions            - referenzieren Client
        // 7) Client-RatingImage-Datei
        // 8) Client selbst
        foreach ($client->getWalks() as $walk) {
            foreach ($walk->getWayPoints() as $wayPoint) {
                if ($wayPoint->getImageName()) {
                    $this->wayPointImageStorage->delete($wayPoint->getImageName());
                }
                $this->entityManager->remove($wayPoint);
            }
        }
        $this->entityManager->flush();

        foreach ($client->getWalks() as $walk) {
            $this->entityManager->remove($walk);
        }
        $this->entityManager->flush();

        foreach ($client->getTags() as $tag) {
            $this->entityManager->remove($tag);
        }

        foreach ($client->getUsers() as $user) {
            $this->entityManager->remove($user);
        }

        foreach ($client->getTeams() as $team) {
            $this->entityManager->remove($team);
        }

        foreach ($client->getSystemicQuestions() as $systemicQuestion) {
            $this->entityManager->remove($systemicQuestion);
        }

        if ($client->getRatingImageName()) {
            $this->clientRatingImageStorage->delete($client->getRatingImageName());
        }

        $this->entityManager->remove($client);
        $this->entityManager->flush();
    }
}
