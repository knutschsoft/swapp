<?php
declare(strict_types=1);

namespace App\Dto;

use App\Entity\Tag;
use App\Entity\Walk;
use App\Serializer\Normalizer\Base64DataUriNormalizer;
use App\Value\AgeGroup;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

final class WayPointAddRequest
{
    /**
     * @var Walk
     *
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Type(type="App\Entity\Walk")
     */
    public Walk $walk;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Length(min="3", max="300")
     */
    public string $locationName;

    /**
     * @var string
     *
     * @Assert\NotNull
     * @Assert\Length(min="0", max="2500")
     */
    public string $note;

    public ?string $imageFileData;
    /**
     * @Assert\Length(min="5", max="200")
     */
    public ?string $imageFileName;
    /**
     * @var AgeGroup[]
     *
     * @Assert\NotNull
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\NotNull,
     *     @Assert\Type(type="App\Value\AgeGroup")
     * })
     */
    public array $ageGroups;
    /**
     * @var Tag[]
     *
     * @Assert\NotNull
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\NotNull,
     *     @Assert\Type(type="App\Entity\Tag")
     * })
     */
    public array $tags;

    /**
     * @Assert\Image(
     *     maxSize="5M",
     *     maxSizeMessage="way-point-add.file.max-size",
     * )
     *
     * @return ?File
     */
    public function getDecodedImageData(): ?File
    {
        if (!$this->imageFileData) {
            return null;
        }
        $normalizer = new Base64DataUriNormalizer();

        return $normalizer->denormalize($this->imageFileData);
    }
}