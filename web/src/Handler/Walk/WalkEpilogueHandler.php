<?php
declare(strict_types=1);

namespace App\Handler\Walk;

use App\Dto\Walk\WalkEpilogueRequest;
use App\Entity\Walk;
use App\Repository\WalkRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class WalkEpilogueHandler implements MessageHandlerInterface
{
    private WalkRepository $walkRepository;

    public function __construct(
        WalkRepository $walkRepository
    ) {
        $this->walkRepository = $walkRepository;
    }

    public function __invoke(WalkEpilogueRequest $request): Walk
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
        $walk->setIsUnfinished(false);

        $this->walkRepository->save($walk);

        return $walk;
    }
}
