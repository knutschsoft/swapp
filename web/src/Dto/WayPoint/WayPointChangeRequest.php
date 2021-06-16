<?php
declare(strict_types=1);

namespace App\Dto\WayPoint;

use App\Entity\Tag;
use App\Entity\WayPoint;
use App\Serializer\Normalizer\Base64DataUriNormalizer;
use App\Value\AgeGroup;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['WayPointChangeRequest', 'SecondGroup'])]
final class WayPointChangeRequest
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type(type: WayPoint::class, groups: ['SecondGroup'])]
    public WayPoint $wayPoint;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 300)]
    public string $locationName;

    #[Assert\NotNull]
    #[Assert\Length(min: 0, max: 2500)]
    public string $note;

    public ?string $imageFileData;

    #[Assert\Length(min: 5, max: 200)]
    public ?string $imageFileName;

    public bool $isMeeting;

    /**
     * @var AgeGroup[]
     *
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\NotNull,
     *     @Assert\Type(type="App\Value\AgeGroup")
     * })
     */
    #[Assert\NotNull]
    public array $ageGroups;
    /**
     * @var Tag[]
     *
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\NotNull,
     *     @Assert\Type(type="App\Entity\Tag")
     * })
     */
    #[Assert\NotNull]
    public array $tags;

    #[Assert\Image(maxSize: "5M", maxSizeMessage: 'way_point.file.max-size')]
    public function getDecodedImageData(): ?File
    {
        if (!$this->imageFileData) {
            return null;
        }
        $normalizer = new Base64DataUriNormalizer();

        return $normalizer->denormalize($this->imageFileData);
    }
}