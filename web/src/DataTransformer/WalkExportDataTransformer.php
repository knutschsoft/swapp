<?php
declare(strict_types=1);

namespace App\DataTransformer;

use App\Entity\Export\WalkExport;
use App\Entity\Walk;
use Carbon\Carbon;

final class WalkExportDataTransformer
{
    public function transform(Walk $walk): WalkExport
    {
        Carbon::setlocale('de');
        $walkExport = new WalkExport();
        $walkExport->id = $walk->getId();
        $walkExport->name = $walk->getName();
        $walkExport->startTime = $walk->getStartTime()->format('d.m.Y H:i:s');
        $walkExport->startTimeTag = $walk->getStartTime()->format('d.m.Y');
        $walkExport->startTimeUhrzeit = $walk->getStartTime()->format('H:i:s');
        $walkExport->startTimeWochentag = (new Carbon($walk->getStartTime()))->isoFormat('dddd');
        $walkExport->endTime = $walk->getEndTime() ? $walk->getEndTime()->format('d.m.Y H:i:s') : '';
        $walkExport->endTimeTag = $walk->getEndTime() ? $walk->getEndTime()->format('d.m.Y') : '';
        $walkExport->endTimeUhrzeit = $walk->getEndTime() ? $walk->getEndTime()->format('H:i:s') : '';
        $walkExport->endTimeWochentag = $walk->getEndTime() ? (new Carbon($walk->getEndTime()))->isoFormat('dddd') : '';
        $walkExport->walkReflection = $walk->getWalkReflection();
        $walkExport->rating = $walk->getRating();
        $walkExport->systemicQuestion = $walk->getSystemicQuestion();
        $walkExport->systemicAnswer = $walk->getSystemicAnswer();
        $walkExport->insights = $walk->getInsights();
        $walkExport->commitments = $walk->getCommitments();
        $walkExport->isResubmission = $walk->getIsResubmission();
        $walkExport->weather = $walk->getWeather();
        $walkExport->isHolidays = $walk->getHolidays();
        $walkExport->conceptOfDay = \implode(',', $walk->getConceptOfDay());
        $walkExport->teamName = $walk->getTeamName();
        $users = [];
        foreach ($walk->getWalkTeamMembers() as $walkTeamMember) {
            $users[] = $walkTeamMember->getUsername();
        }
        $walkExport->users = \implode(',', $users);

        if ($walk->isWithContactsCount()) {
            $walkExport->contactsCount = $walk->getSumOfContactsCount();
        }
        if ($walk->isWithGuests()) {
            $walkExport->guestNames = \implode(',', $walk->getGuestNames());
        }

        $walkExport->peopleCount = $walk->getPeopleCount();
        $walkExport->ageGroups = $walk->isWithAgeRanges() ? $walk->getAgeGroups() : [];
        $walkExport->userGroups = $walk->isWithUserGroups() ? $walk->getUserGroups() : [];

        return $walkExport;
    }
}
