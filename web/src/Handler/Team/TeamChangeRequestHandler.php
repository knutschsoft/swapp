<?php
declare(strict_types=1);

namespace App\Handler\Team;

use App\Dto\Team\TeamChangeRequest;
use App\Entity\Team;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class TeamChangeRequestHandler
{
    public function __construct(private TeamRepository $teamRepository)
    {
    }

    public function __invoke(TeamChangeRequest $request): Team
    {
        $team = $request->team;
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
        $team->setConceptOfDaySuggestions($request->conceptOfDaySuggestions);
        $team->setIsWithContactsCount($request->isWithContactsCount);
        $team->setIsWithUserGroups($request->isWithUserGroups);
        $team->setIsWithSystemicQuestion($request->isWithSystemicQuestion);
        $this->teamRepository->save($team);

        return $team;
    }
}
