<?php
declare(strict_types=1);

namespace App\Handler\WayPoint;

use App\Dto\WayPoint\WayPointRemoveRequest;
use App\Repository\WayPointRepository;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class WayPointRemoveHandler
{
    private WayPointRepository $wayPointRepository;
    private FilesystemOperator $wayPointImageStorage;

    public function __construct(
        WayPointRepository $wayPointRepository,
        FilesystemOperator $wayPointImageStorage
    ) {
        $this->wayPointRepository = $wayPointRepository;
        $this->wayPointImageStorage = $wayPointImageStorage;
    }

    public function __invoke(WayPointRemoveRequest $request): void
    {
        $wayPoint = $request->wayPoint;

        // TODO check for need of manual deletion
        //$wayPoint->setWayPointTags(new ArrayCollection($request->wayPointTags));

        if ($wayPoint->getImageName()) {
            $this->wayPointImageStorage->delete($wayPoint->getImageName());
        }
        $this->wayPointRepository->remove($wayPoint);
    }
}
