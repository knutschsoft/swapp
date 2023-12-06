<?php
declare(strict_types=1);

namespace App\Handler\Walk;

use App\Dto\Walk\WalkRemoveRequest;
use App\Repository\WalkRepository;
use App\Repository\WayPointRepository;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class WalkRemoveRequestHandler
{
    public function __construct(
        private readonly WalkRepository $walkRepository,
        private readonly WayPointRepository $wayPointRepository,
        private readonly FilesystemOperator $wayPointImageStorage
    ) {
    }

    public function __invoke(WalkRemoveRequest $request): void
    {
        $walk = $request->walk;

        foreach ($walk->getWayPoints() as $wayPoint) {
            if ($wayPoint->getImageName()) {
                $this->wayPointImageStorage->delete($wayPoint->getImageName());
            }
            $this->wayPointRepository->remove($wayPoint);
        }

        $this->walkRepository->remove($walk);
    }
}
