<?php
declare(strict_types=1);

namespace App\Handler;

use App\Dto\WalkPrologueRequest;
use App\Entity\Walk;
use App\Repository\SystemicQuestionRepository;
use App\Repository\WalkRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class WalkPrologueHandler implements MessageHandlerInterface
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

    public function __invoke(WalkPrologueRequest $request): Walk
    {
        $systemicQuestion = $this->systemicQuestionRepository->getRandomForClient($request->team->getClient());
        $walk = Walk::prologue($request->team, $systemicQuestion);
        $this->walkRepository->save($walk);

        return $walk;
    }
}
