<?php
declare(strict_types=1);

namespace App\Handler\WayPoint;

use App\Dto\WayPoint\WayPointCreateRequest;
use App\Entity\WayPoint;
use App\Repository\WayPointRepository;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final readonly class WayPointCreateRequestHandler
{
    public function __construct(
        private WayPointRepository $wayPointRepository,
        private FilesystemOperator $wayPointImageStorage
    ) {
    }

    public function __invoke(WayPointCreateRequest $request): WayPoint
    {
        $wayPoint = WayPoint::fromWayPointCreateRequest($request);
        $tmpFile = $request->getDecodedImageData();
        $imageName = $wayPoint->getImageName();
        if ($tmpFile && $imageName) {
            $contents = \file_get_contents(\sprintf("%s%s%s", $tmpFile->getPath(), \DIRECTORY_SEPARATOR, $tmpFile->getFilename()));
            Assert::string($contents);
            $this->wayPointImageStorage->write($imageName, $contents);
        }
        $this->wayPointRepository->save($wayPoint);

        return $wayPoint;
    }
}
