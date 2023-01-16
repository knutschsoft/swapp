<?php
declare(strict_types=1);

namespace App\DataTransformer;

use App\Entity\Export\WayPointExport;
use App\Entity\WayPoint;

final class WayPointExportDataTransformer
{
    public function transform(WayPoint $wayPoint): WayPointExport
    {
        $wayPointExport = new WayPointExport();
        $wayPointExport->id = $wayPoint->getId();
        $wayPointExport->locationName = $wayPoint->getLocationName();
        $wayPointExport->visitedAt = $wayPoint->getVisitedAt()->format('d.m.Y H:i:s');
        $wayPointExport->walkName = $wayPoint->getWalk()->getName();
        $wayPointExport->teamName = $wayPoint->getWalk()->getTeamName();
        $users = [];
        foreach ($wayPoint->getWalk()->getWalkTeamMembers() as $walkTeamMember) {
            $users[] = $walkTeamMember->getUsername();
        }
        $wayPointExport->users = \implode(',', $users);
        $wayPointExport->conceptOfDay = $wayPoint->getWalk()->getConceptOfDay();
        $wayPointExport->note = (string) $wayPoint->getNote();
        $wayPointExport->oneOnOneInterview = $wayPoint->getOneOnOneInterview();
        $wayPointExport->isMeeting = $wayPoint->getIsMeeting();
        $wayPointExport->contactsCount = $wayPoint->getContactsCount();
        $wayPointExport->peopleCount = $wayPoint->getPeopleCount();
        $wayPointExport->userGroups = $wayPoint->getUserGroups();
        $wayPointExport->ageGroups = $wayPoint->getAgeGroups();
        $wayPointExport->tags = $wayPoint->getWayPointTags()->toArray();

        return $wayPointExport;
    }
}
