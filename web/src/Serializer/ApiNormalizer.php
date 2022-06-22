<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Export\WayPointExport;
use App\Entity\WayPoint;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    private NormalizerInterface $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException(\sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }

        $this->decorated = $decorated;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $data = $this->decorated->normalize($object, $format, $context);
        if (!\is_array($data)) {
            return $data;
        }
        if (!$object instanceof WayPoint) {
            return $data;
        }
        if (!isset($context['output'])
            || !isset($context['output']['class'])
            || $context['output']['class'] !== WayPointExport::class
        ) {
            return $data;
        }

        $newData = [];
        foreach ($data as $propertyName => $propertyValue) {
                $newData[$propertyName] = $propertyValue;
        }

        foreach ($this->getCsvUserGroupCells($object) as $label => $csvUserGroupCell) {
            $newData[$label] = $csvUserGroupCell;
        }
        foreach ($this->getCsvAgeCells($object) as $label => $csvAgeCell) {
            $newData[$label] = $csvAgeCell;
        }
        foreach ($object->getWayPointTags() as $tag) {
            if (!isset($newData['Tags'])) {
                $newData['Tags'] = $tag->getName();
            } else {
                $newData['Tags'] .= \sprintf(',%s', $tag->getName());
            }
        }

        return $newData;
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return $this->decorated->supportsDenormalization($data, $type, $format);
    }

    public function denormalize($data, $class, $format = null, array $context = []): mixed
    {
        return $this->decorated->denormalize($data, $class, $format, $context);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }

    /**
     * @param WayPoint $wayPoint
     *
     * @return array<string, int>
     */
    private function getCsvAgeCells(WayPoint $wayPoint): array
    {
        $ageHeaders = [];

        foreach ($wayPoint->getAgeGroups() as $ageGroup) {
            $ageRange = $ageGroup->getAgeRange();
            $label = \sprintf(
                'angetroffene w %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $value = $wayPoint->getFemalesCountForAgeRange($ageRange);
            $ageHeaders[$label] = $value;

            $label = \sprintf(
                'angetroffene m %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $value = $wayPoint->getMalesCountForAgeRange($ageRange);
            $ageHeaders[$label] = $value;

            $label = \sprintf(
                'angetroffene d %s-%s',
                $ageRange->getRangeStart(),
                $ageRange->getRangeEnd()
            );
            $value = $wayPoint->getQueerCountForAgeRange($ageRange);
            $ageHeaders[$label] = $value;
        }

        return $ageHeaders;
    }

    /**
     * @param WayPoint $wayPoint
     *
     * @return array<string, int>
     */
    private function getCsvUserGroupCells(WayPoint $wayPoint): array
    {
        $userGroupHeaders = [];

        foreach ($wayPoint->getUserGroups() as $userGroup) {
            $label = $userGroup->getFrontendLabel();
            $value = $userGroup->getPeopleCount()->getCount();
            $userGroupHeaders[$label] = $value;
        }

        return $userGroupHeaders;
    }
}
