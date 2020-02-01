<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Walk;
use App\Repository\WalkRepository;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WalkExportController
{
    /** @var WalkRepository */
    private $walkRepository;

    public function __construct(
        WalkRepository $walkRepository
    ) {
        $this->walkRepository = $walkRepository;
    }

    /**
     * @Route("walkexport", name="walk_export")
     *
     * @return Response
     */
    public function __invoke(): Response
    {
        $csv = Writer::createFromString();
        $walks = $this->walkRepository->getFindAllQuery()->execute();
        $headers = [
            'Id',
            'Name',
            'Beginn',
            'Ende',
            'Reflexion',
            'Bewertung',
            'systemische Frage',
            'systemische Antwort',
            'Erkenntnisse, Überlegungen, Zielsetzungen',
            'Termine, Besorgungen, Verabredungen',
            'Wiedervorlage Dienstberatung',
            'Wetter',
            'Ferien',
            'Tageskonzept',
            'Teamname',
        ];

        $ageHeaders = [];
        /** @var Walk $walk */
        foreach ($walks as $walk) {
            $ageHeaders = \array_merge($ageHeaders, $this->getCsvAgeHeaders($walk));
        }
        $headers = \array_merge($headers, $ageHeaders);

        $csv->insertOne($headers);

        /** @var Walk $walk */
        foreach ($walks as $walk) {
            $csv->insertOne($this->getCsvContentCells($walk, $ageHeaders));
        }

        return new Response(
            $csv->getContent(),
            200,
            [
                'Content-Type' => 'application/force-download',
                'Content-Disposition' => 'attachment; filename="export.csv"',
            ]
        );
    }

    private function getCsvAgeHeaders(Walk $walk): array
    {
        $ageHeaders = [];
        foreach ($walk->getAgeRanges() as $ageRange) {
            $label = \sprintf(
                'angetroffene w %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $ageHeaders[$label] = $label;

            $label = \sprintf(
                'angetroffene m %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $ageHeaders[$label] = $label;

            $label = \sprintf(
                'angetroffene d %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $ageHeaders[$label] = $label;
        }

        return $ageHeaders;
    }

    private function getCsvContentCells(Walk $walk, array $ageHeaders): array
    {
        $content = [
            $walk->getId(),
            $walk->getName(),
            $walk->getStartTime()->format('d.m.Y H:i:s'),
            $walk->getEndTime()->format('d.m.Y H:i:s'),
            $walk->getWalkReflection(),
            $walk->getRating(),
            $walk->getSystemicQuestion(),
            $walk->getSystemicAnswer(),
            $walk->getInsights(),
            $walk->getCommitments(),
            $walk->getIsResubmission(),
            $walk->getWeather(),
            $walk->getHolidays(),
            $walk->getConceptOfDay(),
            $walk->getTeamName(),
        ];

        foreach ($ageHeaders as $header) {
            $content[$header] = '';
        }
        foreach ($ageHeaders as $header) {
            foreach ($this->getCsvAgeCells($walk) as $label => $csvAgeCell) {
                if ($label === $header) {
                    $content[$header] = $csvAgeCell;
                }
            }
        }

        return $content;
    }

    private function getCsvAgeCells(Walk $walk): array
    {
        $ageHeaders = [];

        foreach ($walk->getAgeRanges() as $ageRange) {
            $label = \sprintf(
                'angetroffene w %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $value = $walk->getFemalesCountForAgeRange($ageRange);
            $ageHeaders[$label] = $value;

            $label = \sprintf(
                'angetroffene m %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $value = $walk->getMalesCountForAgeRange($ageRange);
            $ageHeaders[$label] = $value;

            $label = \sprintf(
                'angetroffene d %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $value = $walk->getQueerCountForAgeRange($ageRange);
            $ageHeaders[$label] = $value;
        }

        return $ageHeaders;
    }
}
