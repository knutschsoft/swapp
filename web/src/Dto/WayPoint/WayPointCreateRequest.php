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
    #[AppAssert\WalkRequirements]
    public Walk $walk;

    #[AppAssert\LocationNameRequirements]
    public string $locationName;

    #[AppAssert\TextareaRequirements]
    public string $note;

    #[AppAssert\TextareaRequirements]
    public string $oneOnOneInterview;

    public ?string $imageFileData = null;

    #[AppAssert\ImageFileNameRequirements]
    public ?string $imageFileName = null;

    #[Assert\Type(type: 'bool')]
    public bool $isMeeting;

    /** @var AgeGroup[] */
    #[AppAssert\AgeGroupsRequirements]
    public array $ageGroups;

    /** @var UserGroup[] */
    #[AppAssert\UserGroupsRequirements]
    public array $userGroups;

    /** @var Tag[] */
    #[AppAssert\TagsRequirements]
    public array $wayPointTags;

    #[AppAssert\ContactsCountRequirements]
    public ?int $contactsCount = null;

    #[AppAssert\PeopleCountRequirements]
    public int $peopleCount;

    #[AppAssert\DateTimeRequirements]
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
