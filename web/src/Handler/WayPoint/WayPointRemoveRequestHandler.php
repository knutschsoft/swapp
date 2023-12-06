<?php
declare(strict_types=1);

namespace App\Handler\WayPoint;

use App\Dto\WayPoint\WayPointRemoveRequest;
use App\Repository\WayPointRepository;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class WayPointRemoveRequestHandler
{
    public function __construct(
        private readonly WayPointRepository $wayPointRepository,
        private readonly FilesystemOperator $wayPointImageStorage
    ) {
    }

    public function __invoke(WayPointRemoveRequest $request): void
    {
        $wayPoint = $request->wayPoint;

        if ($wayPoint->getImageName()) {
            $this->wayPointImageStorage->delete($wayPoint->getImageName());
        }
        $this->wayPointRepository->remove($wayPoint);
    }
}
