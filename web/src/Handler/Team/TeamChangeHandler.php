<?php
declare(strict_types=1);

namespace App\Handler\Team;

use App\Dto\Team\TeamChangeRequest;
use App\Entity\Team;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class TeamChangeHandler implements MessageHandlerInterface
{
    private TeamRepository $teamRepository;

    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function __invoke(TeamChangeRequest $request): Team
    {
        $team = $request->team;
        if ($request->isWithAgeRanges) {
            $team->setAgeRanges($request->ageRanges);
        }
        $team->setIsWithAgeRanges($request->isWithAgeRanges);
        if ($request->userGroupNames) {
            $team->setUserGroupNames($request->userGroupNames);
        }
        $team->setUsers(new ArrayCollection($request->users));
        $team->setName($request->name);
        $team->setLocationNames($request->locationNames);
        $team->setIsWithContactsCount($request->isWithContactsCount);
        $team->setIsWithUserGroups($request->isWithUserGroups);
        $this->teamRepository->save($team);

        return $team;
    }
}
