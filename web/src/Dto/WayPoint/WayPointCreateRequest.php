<?php
declare(strict_types=1);

namespace App\Dto\WayPoint;

use App\Entity\Tag;
use App\Entity\Walk;
use App\Serializer\Normalizer\Base64DataUriNormalizer;
use App\Validator\Constraints as AppAssert;
use App\Value\AgeGroup;
use App\Value\UserGroup;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['WayPointCreateRequest', 'SecondGroup'])]
#[AppAssert\ContactsCount(groups: ['SecondGroup'])]
final class WayPointCreateRequest
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type(type: Walk::class, groups: ['SecondGroup'])]
    public Walk $walk;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 300)]
    public string $locationName;

    #[Assert\NotNull]
    #[Assert\Length(min: 0, max: 2500)]
    public string $note;

    #[Assert\NotNull]
    #[Assert\Length(min: 0, max: 2500)]
    public string $oneOnOneInterview;

    public ?string $imageFileData = null;

    #[Assert\Length(min: 5, max: 200)]
    public ?string $imageFileName = null;

    #[Assert\Type(type: 'bool')]
    public bool $isMeeting;

    /** @var AgeGroup[] */
    #[Assert\All(
        [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type(type: AgeGroup::class),
        ]
    )]
    #[Assert\NotNull]
    public array $ageGroups;

    /** @var UserGroup[] */
    #[AppAssert\UserGroupsRequirements]
    public array $userGroups;

    /** @var Tag[] */
    #[Assert\All(
        [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type(type: Tag::class),
        ]
    )]
    #[Assert\NotNull]
    public array $wayPointTags;

    #[AppAssert\ContactsCountRequirements]
    public ?int $contactsCount = null;

    #[Assert\Sequentially([
        new Assert\NotNull(),
        new Assert\Type(type: \DateTime::class),
    ])]
    public \DateTime $visitedAt;

    #[AppAssert\WayPointImageRequirements]
    public function getDecodedImageData(): ?File
    {
        if (!$this->imageFileData) {
            return null;
        }
        $normalizer = new Base64DataUriNormalizer();

        return $normalizer->denormalize($this->imageFileData);
    }
}
