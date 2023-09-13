<?php
declare(strict_types=1);

namespace App\Handler\Walk;

use App\Dto\Walk\WalkChangeUnfinishedRequest;
use App\Entity\Walk;
use App\Repository\WalkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class WalkChangeUnfinishedRequestHandler
{
    public function __construct(
        private readonly WalkRepository $walkRepository
    ) {
    }

    public function __invoke(WalkChangeUnfinishedRequest $request): Walk
    {
        $walk = $request->walk;
        $walk->setName($request->name);
        $walk->setConceptOfDay($request->conceptOfDay);
        $walk->setWeather($request->weather);
        $walk->setStartTime($request->startTime);
        $walk->setHolidays($request->holidays);
        $walk->setWalkTeamMembers(new ArrayCollection($request->walkTeamMembers));
        if ($walk->isWithGuests()) {
            $walk->setGuestNames($request->guestNames);
        }
        if ($request->walkCreator) {
            $walk->setWalkCreator($request->walkCreator);
        }
        $this->walkRepository->save($walk);

        return $walk;
    }
}
