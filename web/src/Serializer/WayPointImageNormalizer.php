<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\WayPoint;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

final class WayPointImageNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'WAY_POINT_IMAGE_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        private readonly UrlHelper $urlHelper,
        private readonly string $uploadPathWebWayPointImages
    ) {
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return !isset($context[self::ALREADY_CALLED]) && $data instanceof WayPoint;
    }

    /**
     * @inheritDoc
     */
    public function normalize($object, ?string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        \assert($object instanceof WayPoint);

        $imageName = $object->getImageName();
        if ($imageName) {
            $object->setImageSrc($this->getAbsoluteUrl($this->uploadPathWebWayPointImages, $imageName));
        }

        return $this->normalizer->normalize($object, $format, $context);
    }

    private function getAbsoluteUrl(string $uploadPathWeb, string $path): string
    {
        return $this->urlHelper->getAbsoluteUrl(\sprintf('%s%s', $uploadPathWeb, $path));
    }
}
