<?php
declare(strict_types=1);

namespace App\Handler\Walk;

use App\Dto\Walk\WalkChangeRequest;
use App\Entity\Walk;
use App\Repository\WalkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class WalkChangeHandler implements MessageHandlerInterface
{
    private WalkRepository $walkRepository;

    public function __construct(
        WalkRepository $walkRepository
    ) {
        $this->walkRepository = $walkRepository;
    }

    public function __invoke(WalkChangeRequest $request): Walk
    {
        $walk = $request->walk;
        $walk->setName($request->name);
        $walk->setCommitments($request->commitments);
        $walk->setConceptOfDay($request->conceptOfDay);
        $walk->setInsights($request->insights);
        $walk->setSystemicAnswer($request->systemicAnswer);
        $walk->setWalkReflection($request->walkReflection);
        $walk->setWeather($request->weather);
        $walk->setStartTime($request->startTime);
        $walk->setEndTime($request->endTime);
        $walk->setHolidays($request->holidays);
        $walk->setIsResubmission($request->isResubmission);
        $walk->setRating($request->rating);
        $walk->setWalkTeamMembers(new ArrayCollection($request->walkTeamMembers));
        if ($walk->isWithGuests()) {
            $walk->setGuestNames($request->guestNames);
        }
        $this->walkRepository->save($walk);

        return $walk;
    }
}
