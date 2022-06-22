<?php
declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Export\WayPointExport;
use App\Entity\WayPoint;

class WayPointExportDataTransformer implements DataTransformerInterface
{
    /**
     * @param WayPoint $data
     * @param string   $to
     * @param array    $context
     *
     * @return WayPointExport
     */
    public function transform($data, string $to, array $context = []): WayPointExport
    {
        $wayPointExport = new WayPointExport();
        $wayPointExport->id = $data->getId();
        $wayPointExport->locationName = $data->getLocationName();
        $wayPointExport->visitedAt = $data->getVisitedAt()->format('d.m.Y H:i:s');
        $wayPointExport->walkName = $data->getWalk()->getName();
        $wayPointExport->teamName = $data->getWalk()->getTeamName();
        $wayPointExport->note = (string) $data->getNote();
        $wayPointExport->oneOnOneInterview = $data->getOneOnOneInterview();
        $wayPointExport->isMeeting = $data->getIsMeeting();
        $wayPointExport->contactsCount = $data->getContactsCount();
        $wayPointExport->peopleCount = $data->getPeopleCount();

        return $wayPointExport;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return WayPointExport::class === $to && $data instanceof WayPoint;
    }
}
