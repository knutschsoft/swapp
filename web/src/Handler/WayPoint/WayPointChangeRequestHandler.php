<?php
declare(strict_types=1);

namespace App\Handler\WayPoint;

use App\Dto\WayPoint\WayPointChangeRequest;
use App\Entity\WayPoint;
use App\Repository\WayPointRepository;
use Doctrine\Common\Collections\ArrayCollection;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final readonly class WayPointChangeRequestHandler
{
    public function __construct(
        private WayPointRepository $wayPointRepository,
        private FilesystemOperator $wayPointImageStorage
    ) {
    }

    public function __invoke(WayPointChangeRequest $request): WayPoint
    {
        $wayPoint = $request->wayPoint;
        $wayPoint->setLocationName($request->locationName);
        if ($wayPoint->getWalk()->isWithAgeRanges()) {
            $wayPoint->setAgeGroups($request->ageGroups);
            $wayPoint->setPeopleCount($wayPoint->getPeopleCount());
        } elseif ($wayPoint->getWalk()->isWithPeopleCount()) {
            $wayPoint->setPeopleCount($request->peopleCount);
        }
        if ($wayPoint->getWalk()->isWithUserGroups()) {
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
        } elseif ($oldImageName) {
            $this->wayPointImageStorage->delete($oldImageName);
            $wayPoint->unsetImageName();
        }
        $this->wayPointRepository->save($wayPoint);

        return $wayPoint;
    }
}
