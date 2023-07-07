<?php
declare(strict_types=1);

namespace App\Handler\Walk;

use App\Dto\Walk\WalkCreateRequest;
use App\Entity\Walk;
use App\Repository\SystemicQuestionRepository;
use App\Repository\WalkRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class WalkCreateHandler
{
    public function __construct(
        private readonly WalkRepository $walkRepository,
        private readonly SystemicQuestionRepository $systemicQuestionRepository
    ) {
    }

    public function __invoke(WalkCreateRequest $request): Walk
    {
        $systemicQuestion = $this->systemicQuestionRepository->getRandomForClient($request->team->getClient());
        $walk = Walk::fromWalkCreateRequest($request);
        if ($request->team->isWithSystemicQuestion()) {
            $walk->setIsWithSystemicQuestion(true);
            $walk->setSystemicQuestion($systemicQuestion->getQuestion());
        }
        $this->walkRepository->save($walk);

        return $walk;
    }
}
