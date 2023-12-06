<?php
declare(strict_types=1);

namespace App\Handler\Walk;

use App\Dto\Walk\WalkRemoveRequest;
use App\Repository\WalkRepository;
use App\Repository\WayPointRepository;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class WalkRemoveRequestHandler
{
    public function __construct(
        private WalkRepository $walkRepository,
        private WayPointRepository $wayPointRepository,
        private FilesystemOperator $wayPointImageStorage
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
