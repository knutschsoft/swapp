<?php
declare(strict_types=1);

namespace App\Handler;

use App\Entity\CreateWalkPrologueRequest;
use App\Entity\Walk;
use App\Repository\SystemicQuestionRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\WalkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

final class CreateWalkPrologueHandler implements MessageHandlerInterface
{
    private WalkRepository $walkRepository;
    private Security $security;
    private SystemicQuestionRepository $systemicQuestionRepository;
    private UserRepository $userRepository;
    private TeamRepository $teamRepository;

    public function __construct(
        TeamRepository $teamRepository,
        WalkRepository $walkRepository,
        UserRepository $userRepository,
        SystemicQuestionRepository $systemicQuestionRepository,
        Security $security
    ) {
        $this->walkRepository = $walkRepository;
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->systemicQuestionRepository = $systemicQuestionRepository;
        $this->teamRepository = $teamRepository;
    }

    public function __invoke(CreateWalkPrologueRequest $createWalkPrologueRequest): Walk
    {
        $team = $this->teamRepository->findOneById($createWalkPrologueRequest->teamId);

        \assert(null !== $team, 'team not found');

        $systemicQuestion = $this->systemicQuestionRepository->getRandom();
        $walk = Walk::prologue($team, $systemicQuestion);

        $this->walkRepository->save($walk);
        foreach ($team->getUsers() as $user) {
            $user->setWalks(new ArrayCollection([$walk]));
            $this->userRepository->save($user);
        }

        return $walk;
    }
}
