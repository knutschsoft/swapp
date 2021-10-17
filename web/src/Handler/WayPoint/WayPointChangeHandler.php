<?php
declare(strict_types=1);

namespace App\Handler\WayPoint;

use App\Dto\WayPoint\WayPointChangeRequest;
use App\Entity\WayPoint;
use App\Repository\WayPointRepository;
use Doctrine\Common\Collections\ArrayCollection;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class WayPointChangeHandler implements MessageHandlerInterface
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

    public function __invoke(WayPointChangeRequest $request): WayPoint
    {
        $wayPoint = $request->wayPoint;
        $wayPoint->setLocationName($request->locationName);
        $wayPoint->setAgeGroups($request->ageGroups);
        $wayPoint->setNote($request->note);
        $wayPoint->setOneOnOneInterview($request->oneOnOneInterview);
        $wayPoint->setImageName((string) $request->imageFileName);
        $wayPoint->setIsMeeting($request->isMeeting);
        $wayPoint->setWayPointTags(new ArrayCollection($request->wayPointTags));

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
