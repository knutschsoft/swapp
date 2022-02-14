<?php
declare(strict_types=1);

namespace App\Serializer\Normalizer;

use enshrined\svgSanitize\Sanitizer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;
use Webmozart\Assert\Assert;

class Base64DataUriNormalizer extends DataUriNormalizer
{
    /**
     * @inheritDoc
     */
    public function denormalize($data, $type = File::class, $format = null, array $context = []): File
    {
        parent::denormalize($data, $type, $format, $context);

        Assert::string($data);
        $match = [];
        // @codingStandardsIgnoreStart
        \preg_match('/^data:(?P<mimeType>[a-z0-9][a-z0-9\!\#\$\&\-\^\_\+\.]{0,126}\/[a-z0-9][a-z0-9\!\#\$\&\-\^\_\+\.]{0,126}(;[a-z0-9\-]+\=[a-z0-9\-]+)?)?;base64,(?P<encoded>.+)$/i', $data, $match);
        // @codingStandardsIgnoreEnd

        if (!\array_key_exists('mimeType', $match)) {
            throw new NotNormalizableValueException('The MimeType should be specified in the URI.');
        }

        $extensionGuesser = MimeTypes::getDefault();
        $extensions = $extensionGuesser->getExtensions($match['mimeType']);

        $filesystem = new Filesystem();
        $tempfile = "{$filesystem->tempnam('/tmp', 'symfony')}.$extensions[0]";
        $content = \base64_decode($match['encoded'], true);
        Assert::string($content);
        if (\str_starts_with(\strtolower($extensions[0]), 'svg')) {
            $sanitizer = new Sanitizer();
            $content = $sanitizer->sanitize($content);
        }

        $filesystem->dumpFile("$tempfile", $content);

        return new File($tempfile);
    }
}
