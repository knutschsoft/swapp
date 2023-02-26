<?php
declare(strict_types=1);

namespace App\Entity\Export;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\DataTransformer\WayPointExportProvider;
use App\Entity\Tag;
use App\Entity\WayPoint;
use App\Value\AgeGroup;
use App\Value\AgeRange;
use App\Value\UserGroup;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ApiResource(
    shortName: 'WayPoint',
    operations: [
        new GetCollection(
            uriTemplate: '/way_points/export',
            formats: ['csv' => 'text/csv'],
            status: 200,
            output: WayPointExport::class,
            provider: WayPointExportProvider::class,
            extraProperties: [
                'entity' => WayPoint::class,
            ]
        ),
    ],
    normalizationContext: ['groups' => ['wayPointExport:read']],
)]
class WayPointExport
{
    #[Groups('wayPointExport:read')]
    public int $id;
    #[Groups('wayPointExport:read')]
    #[SerializedName('Ort')]
    public string $locationName;
    #[Groups('wayPointExport:read')]
    #[SerializedName('Ankunft')]
    public string $visitedAt;
    #[Groups('wayPointExport:read')]
    #[SerializedName('Tag')]
    public string $visitedAtTag;
    #[Groups('wayPointExport:read')]
    #[SerializedName('Uhrzeit')]
    public string $visitedAtUhrzeit;
    #[Groups('wayPointExport:read')]
    #[SerializedName('Wochentag')]
    public string $visitedAtWochentag;
    #[Groups('wayPointExport:read')]
    #[SerializedName('Rundenname')]
    public string $walkName;
    #[Groups('wayPointExport:read')]
    #[SerializedName('Teamname')]
    public string $teamName;
    #[Groups('wayPointExport:read')]
    #[SerializedName('Teilnehmende')]
    public string $users;
    #[Groups('wayPointExport:read')]
    #[SerializedName('Tageskonzept')]
    public string $conceptOfDay;
    #[Groups('wayPointExport:read')]
    #[SerializedName('Beobachtung')]
    public string $note;
    #[Groups('wayPointExport:read')]
    #[SerializedName('Einzelgespräch')]
    public string $oneOnOneInterview;
    #[Groups('wayPointExport:read')]
    #[SerializedName('Meeting?')]
    public bool $isMeeting;
    #[Groups('wayPointExport:read')]
    #[SerializedName('direkte Kontakte')]
    public ?int $contactsCount;
    #[Groups('wayPointExport:read')]
    #[SerializedName('Anzahl Personen vor Ort')]
    public int $peopleCount;
    /** @var UserGroup[] */
    public array $userGroups;
    /** @var AgeGroup[] */
    public array $ageGroups;
    /** @var Tag[] */
    public array $tags;

    public function getFemalesCountForAgeRange(AgeRange $ageRange): int
    {
        $sum = 0;
        foreach ($this->ageGroups as $ageGroup) {
            if (!$ageGroup->getGender()->isFemale()) {
                continue;
            }
            if (!$ageGroup->getAgeRange()->equal($ageRange)) {
                continue;
            }
            $sum += $ageGroup->getPeopleCount()->getCount();
        }

        return $sum;
    }

    public function getMalesCountForAgeRange(AgeRange $ageRange): int
    {
        $sum = 0;
        foreach ($this->ageGroups as $ageGroup) {
            if (!$ageGroup->getGender()->isMale()) {
                continue;
            }
            if (!$ageGroup->getAgeRange()->equal($ageRange)) {
                continue;
            }
            $sum += $ageGroup->getPeopleCount()->getCount();
        }

        return $sum;
    }

    public function getQueerCountForAgeRange(AgeRange $ageRange): int
    {
        $sum = 0;
        foreach ($this->ageGroups as $ageGroup) {
            if (!$ageGroup->getGender()->isQueer()) {
                continue;
            }
            if (!$ageGroup->getAgeRange()->equal($ageRange)) {
                continue;
            }
            $sum += $ageGroup->getPeopleCount()->getCount();
        }

        return $sum;
    }
}
