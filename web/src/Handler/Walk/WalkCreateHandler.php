<?php
declare(strict_types=1);

namespace App\Handler\Walk;

use App\Dto\Walk\WalkCreateRequest;
use App\Entity\Walk;
use App\Repository\SystemicQuestionRepository;
use App\Repository\WalkRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class WalkCreateHandler implements MessageHandlerInterface
{
    private WalkRepository $walkRepository;
    private SystemicQuestionRepository $systemicQuestionRepository;

    public function __construct(
        WalkRepository $walkRepository,
        SystemicQuestionRepository $systemicQuestionRepository
    ) {
        $this->walkRepository = $walkRepository;
        $this->systemicQuestionRepository = $systemicQuestionRepository;
    }

    public function __invoke(WalkCreateRequest $request): Walk
    {
        $systemicQuestion = $this->systemicQuestionRepository->getRandomForClient($request->team->getClient());
        $walk = Walk::fromWalkCreateRequest($request, $systemicQuestion);
        $this->walkRepository->save($walk);

        return $walk;
    }
}
