<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Walk;
use App\Repository\SystemicQuestionRepository;
use App\Repository\UserRepository;
use App\Repository\WalkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Routing\Annotation\Route;

final class CreateWalkPrologueController
{
    private WalkRepository $walkRepository;
    private SystemicQuestionRepository $systemicQuestionRepository;
    private UserRepository $userRepository;

    public function __construct(
        WalkRepository $walkRepository,
        UserRepository $userRepository,
        SystemicQuestionRepository $systemicQuestionRepository
    ) {
        $this->walkRepository = $walkRepository;
        $this->userRepository = $userRepository;
        $this->systemicQuestionRepository = $systemicQuestionRepository;
    }

    /**
     * @param Team    $data
     * @param ?string $teamId
     *
     * @return Walk
     *
     * @Route(
     *     name="create_walk_prologue",
     *     path="/api/walk/create-prologue",
     *     methods={"POST"},
     *     defaults={
     *         "_api_resource_class"=Walk::class,
     *         "_api_collection_operation_name"="post_prologue"
     *     }
     * )
     */
    public function __invoke(Team $data, ?string $teamId): Walk
    {
        $systemicQuestion = $this->systemicQuestionRepository->getRandom();
        $walk = Walk::prologue($data, $systemicQuestion);

        $this->walkRepository->save($walk);
        foreach ($data->getUsers() as $user) {
            $user->setWalks(new ArrayCollection([$walk]));
            $this->userRepository->save($user);
        }

        return $walk;
    }
}
