<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Export\WalkExport;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class WalkExportNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const string ALREADY_CALLED = 'WALK_EXPORT_NORMALIZER_ALREADY_CALLED';

    /**
     * @inheritDoc
     */
    public function normalize($object, $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        \assert($object instanceof WalkExport);
        $context[self::ALREADY_CALLED] = true;
        $data = $this->normalizer->normalize($object, $format, $context);
        if (!isset($context['output'])
            || !\is_array($context['output'])
            || !isset($context['output']['class'])
            || $context['output']['class'] !== WalkExport::class
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

        return $data instanceof WalkExport;
    }

    /**
     * @param WalkExport $walkExport
     *
     * @return array<string, int>
     */
    private function getCsvAgeCells(WalkExport $walkExport): array
    {
        $ageHeaders = [];

        foreach ($walkExport->ageGroups as $ageGroup) {
            $ageRange = $ageGroup->getAgeRange();
            $label = \sprintf(
                'angetroffene w %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $value = $walkExport->getFemalesCountForAgeRange($ageRange);
            $ageHeaders[$label] = $value;

            $label = \sprintf(
                'angetroffene m %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $value = $walkExport->getMalesCountForAgeRange($ageRange);
            $ageHeaders[$label] = $value;

            $label = \sprintf(
                'angetroffene d %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $value = $walkExport->getQueerCountForAgeRange($ageRange);
            $ageHeaders[$label] = $value;
        }

        return $ageHeaders;
    }

    /**
     * @param WalkExport $walkExport
     *
     * @return array<string, int>
     */
    private function getCsvAdditionalCells(WalkExport $walkExport): array
    {
        $additionalHeaders = [];
        $dataSets = [
            $walkExport->userGroups,
            $walkExport->consumables,
            $walkExport->counselings,
            $walkExport->medicals,
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
