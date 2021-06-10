<?php
declare(strict_types=1);

namespace App\Handler;

use App\Dto\WalkExportRequest;
use App\Entity\Walk;
use App\Repository\WalkRepository;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class WalkExportHandler implements MessageHandlerInterface
{
    private WalkRepository $walkRepository;

    public function __construct(WalkRepository $walkRepository)
    {
        $this->walkRepository = $walkRepository;
    }

    public function __invoke(WalkExportRequest $request): Response
    {
        $csv = Writer::createFromString();

        $walks = $this->walkRepository->findForExport($request->client, $request->startTimeFrom, $request->startTimeTo);
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
        foreach ($walks as $walk) {
            \assert($walk instanceof Walk);
            $ageHeaders = \array_merge($ageHeaders, $this->getCsvAgeHeaders($walk));
        }
        $headers = \array_merge($headers, $ageHeaders);

        $csv->insertOne($headers);

        foreach ($walks as $walk) {
            \assert($walk instanceof Walk);
            $csv->insertOne($this->getCsvContentCells($walk, $ageHeaders));
        }

        return new Response(
            $csv->toString(),
            200,
            [
                'Content-Type' => 'application/force-download',
                'Content-Disposition' => 'attachment; filename="export.csv"',
            ]
        );
    }

    /**
     * @param Walk $walk
     *
     * @return array<int|string, bool|int|string|null>
     */
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

    /**
     * @param Walk                                    $walk
     * @param array<int|string, bool|int|string|null> $ageHeaders
     *
     * @return array<int|string, string>
     */
    private function getCsvContentCells(Walk $walk, array $ageHeaders): array
    {
        $content = [
            (string) $walk->getId(),
            $walk->getName(),
            $walk->getStartTime()->format('d.m.Y H:i:s'),
            $walk->getEndTime()->format('d.m.Y H:i:s'),
            $walk->getWalkReflection(),
            (string) $walk->getRating(),
            $walk->getSystemicQuestion(),
            $walk->getSystemicAnswer(),
            (string) $walk->getInsights(),
            (string) $walk->getCommitments(),
            (string) $walk->getIsResubmission(),
            $walk->getWeather(),
            (string) $walk->getHolidays(),
            $walk->getConceptOfDay(),
            $walk->getTeamName(),
        ];

        foreach ($ageHeaders as $header) {
            $content[$header] = '';
        }
        foreach ($ageHeaders as $header) {
            foreach ($this->getCsvAgeCells($walk) as $label => $csvAgeCell) {
                if ($label === $header) {
                    $content[$header] = (string) $csvAgeCell;
                }
            }
        }

        return $content;
    }

    /**
     * @param Walk $walk
     *
     * @return array<string, int>
     */
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
