<?php
declare(strict_types=1);

namespace App\Handler\Team;

use App\Dto\Team\TeamCreateRequest;
use App\Entity\Team;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TeamCreateHandler
{
    public function __construct(private readonly TeamRepository $teamRepository)
    {
    }

    public function __invoke(TeamCreateRequest $request): Team
    {
        $team = new Team();
        $team->updateClient($request->client);
        if ($request->isWithAgeRanges) {
            $team->setAgeRanges($request->ageRanges);
        }
        $team->setIsWithAgeRanges($request->isWithAgeRanges);
        $team->setIsWithPeopleCount($request->isWithPeopleCount);
        $team->setUserGroupNames($request->userGroupNames);
        $team->setUsers(new ArrayCollection($request->users));
        $team->setName($request->name);
        $team->setIsWithGuests($request->isWithGuests);
        if ($request->isWithGuests) {
            $team->setGuestNames($request->guestNames);
        }
        $team->setLocationNames($request->locationNames);
        $team->setWalkNames($request->walkNames);
        $team->setIsWithContactsCount($request->isWithContactsCount);
        $team->setIsWithUserGroups($request->isWithUserGroups);
        $this->teamRepository->save($team);

        return $team;
    }
}
