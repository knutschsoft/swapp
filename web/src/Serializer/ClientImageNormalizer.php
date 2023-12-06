<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Client;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

final class ClientImageNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const string ALREADY_CALLED = 'CLIENT_IMAGE_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        private readonly UrlHelper $urlHelper,
        private readonly string $uploadPathWebClientRatingImages
    ) {
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return !isset($context[self::ALREADY_CALLED]) && $data instanceof Client;
    }

    /**
     * @inheritDoc
     */
    public function normalize($object, ?string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        \assert($object instanceof Client);

        $imageName = $object->getRatingImageName();
        if ($imageName) {
            $object->setRatingImageSrc($this->getAbsoluteUrl($this->uploadPathWebClientRatingImages, $imageName));
        }

        return $this->normalizer->normalize($object, $format, $context);
    }

    private function getAbsoluteUrl(string $uploadPathWeb, string $path): string
    {
        return $this->urlHelper->getAbsoluteUrl(\sprintf('%s%s', $uploadPathWeb, $path));
    }
}
