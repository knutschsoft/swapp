<?php
declare(strict_types=1);

namespace App\Handler\Walk;

use App\Dto\Walk\WalkChangeStartTimeRequest;
use App\Entity\Walk;
use App\Repository\WalkRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class WalkChangeStartTimeRequestHandler
{
    public function __construct(
        private readonly WalkRepository $walkRepository
    ) {
    }

    public function __invoke(WalkChangeStartTimeRequest $request): Walk
    {
        $walk = $request->walk;
        $walk->setStartTime($request->startTime);
        $this->walkRepository->save($walk);

        return $walk;
    }
}
