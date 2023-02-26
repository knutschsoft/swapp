<?php
declare(strict_types=1);

namespace App\Entity\Export;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\DataTransformer\WalkExportProvider;
use App\Entity\Walk;
use App\Value\AgeGroup;
use App\Value\AgeRange;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ApiResource(
    shortName: 'Walk',
    operations: [
        new GetCollection(
            uriTemplate: '/walks/export',
            formats: ['csv' => 'text/csv'],
            status: 200,
            openapiContext: ['summary' => 'Exports all walks for given filter as csv.'],
            output: WalkExport::class,
            provider: WalkExportProvider::class,
            extraProperties: [
                'entity' => Walk::class,
            ]
        ),
    ],
    normalizationContext: ['groups' => ['walkExport:read']],
)]
class WalkExport
{
    #[Groups('walkExport:read')]
    #[SerializedName('Id')]
    public int $id;
    #[Groups('walkExport:read')]
    #[SerializedName('Name')]
    public string $name;
    #[Groups('walkExport:read')]
    #[SerializedName('Beginn')]
    public string $startTime;
    #[Groups('walkExport:read')]
    #[SerializedName('Beginn Wochentag')]
    public string $startTimeWochentag;
    #[Groups('walkExport:read')]
    #[SerializedName('Ende')]
    public string $endTime;
    #[Groups('walkExport:read')]
    #[SerializedName('Ende Wochentag')]
    public string $endTimeWochentag;
    #[Groups('walkExport:read')]
    #[SerializedName('Reflexion')]
    public string $walkReflection;
    #[Groups('walkExport:read')]
    #[SerializedName('Bewertung')]
    public int $rating;
    #[Groups('walkExport:read')]
    #[SerializedName('systemische Frage')]
    public string $systemicQuestion;
    #[Groups('walkExport:read')]
    #[SerializedName('systemische Antwort')]
    public string $systemicAnswer;
    #[Groups('walkExport:read')]
    #[SerializedName('Erkenntnisse, Überlegungen, Zielsetzungen')]
    public string $insights;
    #[Groups('walkExport:read')]
    #[SerializedName('Termine, Besorgungen, Verabredungen')]
    public string $commitments;
    #[Groups('walkExport:read')]
    #[SerializedName('Wiedervorlage Dienstberatung')]
    public bool $isResubmission;
    #[Groups('walkExport:read')]
    #[SerializedName('Wetter')]
    public string $weather;
    #[Groups('walkExport:read')]
    #[SerializedName('Ferien')]
    public bool $isHolidays;
    #[Groups('walkExport:read')]
    #[SerializedName('Tageskonzept')]
    public string $conceptOfDay;
    #[Groups('walkExport:read')]
    #[SerializedName('Teamname')]
    public string $teamName;
    #[Groups('walkExport:read')]
    #[SerializedName('Anzahl direkter Kontakte')]
    public ?int $contactsCount;
    #[Groups('walkExport:read')]
    #[SerializedName('Weitere Teilnehmende')]
    public ?string $guestNames;
    #[Groups('walkExport:read')]
    #[SerializedName('Anzahl Personen vor Ort')]
    public int $peopleCount;
    /** @var AgeGroup[] */
    public array $ageGroups;

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
