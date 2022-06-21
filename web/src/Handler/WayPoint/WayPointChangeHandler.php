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
        if ($request->userGroups && $wayPoint->getWalk()->isWithUserGroups()) {
            $wayPoint->setUserGroups($request->userGroups);
        }
        $wayPoint->setNote($request->note);
        $wayPoint->setOneOnOneInterview($request->oneOnOneInterview);
        $wayPoint->setIsMeeting($request->isMeeting);
        $wayPoint->setWayPointTags(new ArrayCollection($request->wayPointTags));
        if ($wayPoint->getWalk()->isWithContactsCount()) {
            $wayPoint->setContactsCount($request->contactsCount);
        }
        $wayPoint->setVisitedAt($request->visitedAt);

        $tmpFile = $request->getDecodedImageData();
        $oldImageName = $wayPoint->getImageName();
        if ($tmpFile && $request->imageFileName) {
            $newImageName = \sprintf("%s_%s", \time(), $request->imageFileName);
            $wayPoint->setImageName($newImageName);

            $contents = \file_get_contents(\sprintf("%s%s%s", $tmpFile->getPath(), \DIRECTORY_SEPARATOR, $tmpFile->getFilename()));
            Assert::string($contents);
            $this->wayPointImageStorage->write($newImageName, $contents);
            if ($newImageName !== $oldImageName && $oldImageName) {
                $this->wayPointImageStorage->delete($oldImageName);
            }
        }
        $this->wayPointRepository->save($wayPoint);

        return $wayPoint;
    }
}
