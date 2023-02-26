<?php
declare(strict_types=1);

namespace App\DataTransformer;

use App\Entity\Export\WalkExport;
use App\Entity\Walk;

final class WalkExportDataTransformer
{
    public function transform(Walk $walk): WalkExport
    {
        $walkExport = new WalkExport();
        $walkExport->id = $walk->getId();
        $walkExport->name = $walk->getName();
        $walkExport->startTime = $walk->getStartTime()->format('d.m.Y H:i:s');
        $walkExport->endTime = $walk->getEndTime() ? $walk->getEndTime()->format('d.m.Y H:i:s') : '';
        $walkExport->walkReflection = $walk->getWalkReflection();
        $walkExport->rating = $walk->getRating();
        $walkExport->systemicQuestion = $walk->getSystemicQuestion();
        $walkExport->systemicAnswer = $walk->getSystemicAnswer();
        $walkExport->insights = $walk->getInsights();
        $walkExport->commitments = $walk->getCommitments();
        $walkExport->isResubmission = $walk->getIsResubmission();
        $walkExport->weather = $walk->getWeather();
        $walkExport->isHolidays = $walk->getHolidays();
        $walkExport->conceptOfDay = $walk->getConceptOfDay();
        $walkExport->teamName = $walk->getTeamName();
        if ($walk->isWithContactsCount()) {
            $walkExport->contactsCount = $walk->getSumOfContactsCount();
        }
        if ($walk->isWithGuests()) {
            $walkExport->guestNames = \implode(',', $walk->getGuestNames());
        }

        $walkExport->peopleCount = $walk->getPeopleCount();
        if ($walk->isWithAgeRanges()) {
            $walkExport->ageGroups = $walk->getAgeGroups();
        }

        return $walkExport;
    }
}
