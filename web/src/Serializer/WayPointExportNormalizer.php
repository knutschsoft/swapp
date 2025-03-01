<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Export\WayPointExport;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class WayPointExportNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const string ALREADY_CALLED = 'WAY_POINT_EXPORT_NORMALIZER_ALREADY_CALLED';

    /**
     * @inheritDoc
     */
    public function normalize($object, $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        \assert($object instanceof WayPointExport);
        $context[self::ALREADY_CALLED] = true;
        $data = $this->normalizer->normalize($object, $format, $context);
        if (!isset($context['output'])
            || !isset($context['output']['class'])
            || $context['output']['class'] !== WayPointExport::class
        ) {
            return $data;
        }

        $newData = [];
        Assert::isArray($data);
        foreach ($data as $propertyName => $propertyValue) {
            $newData[$propertyName] = $propertyValue;
        }

        foreach ($this->getCsvAdditionalCells($object) as $label => $csvAdditionalCell) {
            $newData[$label] = $csvAdditionalCell;
        }
        foreach ($this->getCsvAgeCells($object) as $label => $csvAgeCell) {
            $newData[$label] = $csvAgeCell;
        }
        foreach ($object->tags as $tag) {
            if (empty($newData['Tags'])) {
                $newData['Tags'] = $tag->getName();
            } else {
                $newData['Tags'] .= \sprintf(',%s', $tag->getName());
            }
        }

        return $newData;
    }

    /**
     * @param mixed                $data
     * @param string|null          $format
     * @param array<string, mixed> $context
     *
     * @return bool
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        // Make sure we're not called twice
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof WayPointExport;
    }

    /**
     * @param WayPointExport $wayPointExport
     *
     * @return array<string, int>
     */
    private function getCsvAgeCells(WayPointExport $wayPointExport): array
    {
        $ageHeaders = [];

        foreach ($wayPointExport->ageGroups as $ageGroup) {
            $ageRange = $ageGroup->getAgeRange();
            $label = \sprintf(
                'angetroffene w %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $value = $wayPointExport->getFemalesCountForAgeRange($ageRange);
            $ageHeaders[$label] = $value;

            $label = \sprintf(
                'angetroffene m %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $value = $wayPointExport->getMalesCountForAgeRange($ageRange);
            $ageHeaders[$label] = $value;

            $label = \sprintf(
                'angetroffene d %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $value = $wayPointExport->getQueerCountForAgeRange($ageRange);
            $ageHeaders[$label] = $value;
        }

        return $ageHeaders;
    }

    /**
     * @param WayPointExport $wayPointExport
     *
     * @return array<string, int>
     */
    private function getCsvAdditionalCells(WayPointExport $wayPointExport): array
    {
        $additionalHeaders = [];
        $dataSets = [
            $wayPointExport->userGroups,
            $wayPointExport->consumables,
            $wayPointExport->counselings,
            $wayPointExport->medicals,
        ];

        foreach ($dataSets as $dataSet) {
            foreach ($dataSet as $item) {
                $label = $item->getFrontendLabel();
                $value = $item->getPeopleCount()->getCount();
                $additionalHeaders[$label] = ($additionalHeaders[$label] ?? 0) + $value;
            }
        }

        return $additionalHeaders;
    }
}
