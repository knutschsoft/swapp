<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Dto\WayPoint\WayPointChangeRequest;
use App\Dto\WayPoint\WayPointCreateRequest;
use App\Dto\WayPoint\WayPointRemoveRequest;
use App\Repository\DoctrineORMWayPointRepository;
use App\Security\Voter\WalkVoter;
use App\Security\Voter\WayPointVoter;
use App\Value\AgeGroup;
use App\Value\AgeRange;
use App\Value\Gender;
use App\Value\PeopleCount;
use App\Value\UserGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            uriTemplate: '/way_points/change',
            status: 200,
            securityPostDenormalize: 'is_granted("'.WayPointVoter::EDIT.'", object.wayPoint)',
            input: WayPointChangeRequest::class,
            output: WayPoint::class,
            messenger: 'input',
        ),
        new Post(
            uriTemplate: '/way_points/create',
            status: 200,
            securityPostDenormalize: 'is_granted("'.WalkVoter::READ.'", object.walk)',
            input: WayPointCreateRequest::class,
            output: WayPoint::class,
            messenger: 'input'
        ),
        new Post(
            uriTemplate: '/way_points/remove',
            status: 200,
            securityPostDenormalize: 'is_granted("'.WayPointVoter::REMOVE.'", object.wayPoint)',
            input: WayPointRemoveRequest::class,
            messenger: 'input'
        ),
    ],
    normalizationContext: ['groups' => ['wayPoint:read']],
    order: ['locationName' => 'ASC'],
)]
#[ORM\Table(name: 'way_point')]
#[ORM\Entity(repositoryClass: DoctrineORMWayPointRepository::class)]
#[ApiFilter(filterClass: OrderFilter::class, properties: [
    'walk.updatedAt',
    'locationName',
    'oneOnOneInterview',
    'note',
    'visitedAt',
    'walk.name',
    'walk.startTime',
    'walk.teamName',
])]
#[ApiFilter(filterClass: BooleanFilter::class, properties: ['isMeeting'])]
#[ApiFilter(filterClass: DateFilter::class, properties: ['visitedAt'])]
#[ApiFilter(filterClass: SearchFilter::class, properties: [
    'locationName' => 'partial',
    'note' => 'partial',
    'oneOnOneInterview' => 'partial',
    'wayPointTags' => 'exact',
    'walk.teamName' => 'partial',
    'walk' => 'exact',
])]
class WayPoint
{
    use TimestampableEntity;

    /** @Gedmo\Timestampable(on="create") **/
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected $createdAt; // phpcs:ignore

    /** @Gedmo\Timestampable(on="create") **/
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected $updatedAt; // phpcs:ignore

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected \DateTimeInterface $visitedAt;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue()]
    private int $id;

    #[ORM\Column(name: 'image_name', type: 'string', length: 255, nullable: true)]
    private ?string $imageName = null;
    private string $imageSrc;

    /** @MaxDepth(2) */
    #[ORM\ManyToOne(targetEntity: Walk::class, inversedBy: 'wayPoints')]
    private Walk $walk;

    #[ORM\Column(type: 'string', length: 4096)]
    private string $locationName;

    /** @var AgeGroup[] */
    #[ORM\Column(type: 'json_document')]
    private array $ageGroups;

    /** @var UserGroup[] */
    #[ORM\Column(type: 'json_document')]
    private array $userGroups;

    #[ORM\Column(type: 'string', length: 4096, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(type: 'string', length: 4096)]
    private string $oneOnOneInterview;

    #[ORM\Column(type: 'boolean')]
    private bool $isMeeting;

    /** @var Collection<int, Tag> */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'wayPoints')]
    private Collection $wayPointTags;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $peopleCount;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $contactsCount;

    public function __construct()
    {
        $this->wayPointTags = new ArrayCollection();
        $this->ageGroups = [];
        $this->userGroups = [];
        $this->locationName = '';
        $this->isMeeting = false;
        $this->note = '';
        $this->oneOnOneInterview = '';
        $this->imageSrc = '';
        $this->contactsCount = null;
        $this->peopleCount = 0;
    }

    public static function fromWalk(Walk $walk): self
    {
        $instance = new self();

        $instance->setWalk($walk);
        foreach ($walk->getAgeRanges() as $ageRange) {
            $ageGroup = AgeGroup::fromRangeGenderAndCount($ageRange, Gender::fromString('m'), PeopleCount::none());
            $instance->addAgeGroup($ageGroup);
            $ageGroup = AgeGroup::fromRangeGenderAndCount($ageRange, Gender::fromString('w'), PeopleCount::none());
            $instance->addAgeGroup($ageGroup);
            $ageGroup = AgeGroup::fromRangeGenderAndCount($ageRange, Gender::fromString('x'), PeopleCount::none());
            $instance->addAgeGroup($ageGroup);
        }

        return $instance;
    }

    public static function fromWayPointCreateRequest(WayPointCreateRequest $request): self
    {
        $instance = new self();

        $instance->setWalk($request->walk);
        if ($request->walk->isWithAgeRanges()) {
            $instance->setAgeGroups($request->ageGroups);
            $instance->setPeopleCount($instance->getPeopleCount());
        } elseif ($request->walk->isWithPeopleCount()) {
            $instance->setPeopleCount($request->peopleCount);
        }
        if ($instance->getWalk()->isWithUserGroups()) {
            $instance->setUserGroups($request->userGroups);
        }
        $instance->setWayPointTags(new ArrayCollection($request->wayPointTags));
        if ($request->walk->isWithContactsCount()) {
            $instance->setContactsCount($request->contactsCount);
        }
        $instance->setNote($request->note);
        $instance->setOneOnOneInterview($request->oneOnOneInterview);
        $instance->setLocationName($request->locationName);
        if ($request->imageFileName) {
            $instance->setImageName(\sprintf("%s_%s", \time(), $request->imageFileName));
        }
        $instance->setIsMeeting($request->isMeeting);
        $instance->setVisitedAt($request->visitedAt);

        return $instance;
    }

    /**
     * @return UserGroup[]
     */
    #[Groups(['wayPoint:read'])]
    public function getUserGroups(): array
    {
        return $this->userGroups;
    }

    /**
     * @param UserGroup[] $userGroups
     */
    public function setUserGroups(array $userGroups): void
    {
        $this->userGroups = $userGroups;
    }

    /**
     * @return AgeGroup[]
     */
    #[Groups(['wayPoint:read'])]
    public function getAgeGroups(): array
    {
        return $this->ageGroups;
    }

    /**
     * @param AgeGroup[] $ageGroups
     */
    public function setAgeGroups(array $ageGroups): void
    {
        $this->ageGroups = $ageGroups;
    }

    public function addAgeGroup(AgeGroup $ageGroup): void
    {
        $this->ageGroups[] = $ageGroup;
    }

    #[Groups(['wayPoint:read'])]
    public function getPeopleCount(): int
    {
        if ($this->getWalk()->isWithAgeRanges()) {
            $sum = 0;
            foreach ($this->getAgeGroups() as $ageGroup) {
                $sum += $ageGroup->getPeopleCount()->getCount();
            }

            return $sum;
        }

        return $this->peopleCount;
    }

    public function setPeopleCount(int $peopleCount): void
    {
        $this->peopleCount = $peopleCount;
    }

    #[Groups(['wayPoint:read'])]
    public function getFemalesCount(): int
    {
        $sum = 0;
        foreach ($this->getAgeGroups() as $ageGroup) {
            if (!$ageGroup->getGender()->isFemale()) {
                continue;
            }
            $sum += $ageGroup->getPeopleCount()->getCount();
        }

        return $sum;
    }

    #[Groups(['wayPoint:read'])]
    public function getMalesCount(): int
    {
        $sum = 0;
        foreach ($this->getAgeGroups() as $ageGroup) {
            if (!$ageGroup->getGender()->isMale()) {
                continue;
            }
            $sum += $ageGroup->getPeopleCount()->getCount();
        }

        return $sum;
    }

    #[Groups(['wayPoint:read'])]
    public function getQueerCount(): int
    {
        $sum = 0;
        foreach ($this->getAgeGroups() as $ageGroup) {
            if (!$ageGroup->getGender()->isQueer()) {
                continue;
            }
            $sum += $ageGroup->getPeopleCount()->getCount();
        }

        return $sum;
    }

    public function getFemalesCountForAgeRange(AgeRange $ageRange): int
    {
        $sum = 0;
        foreach ($this->getAgeGroups() as $ageGroup) {
            if (!$ageGroup->getGender()->isFemale()) {
                continue;
            }
            if (!$ageGroup->getAgeRange()->equal($ageRange)) {
                continue;
            }
            $sum += $ageGroup->getPeopleCount()->getCount();
        }

        return $sum;
    }

    public function getMalesCountForAgeRange(AgeRange $ageRange): int
    {
        $sum = 0;
        foreach ($this->getAgeGroups() as $ageGroup) {
            if (!$ageGroup->getGender()->isMale()) {
                continue;
            }
            if (!$ageGroup->getAgeRange()->equal($ageRange)) {
                continue;
            }
            $sum += $ageGroup->getPeopleCount()->getCount();
        }

        return $sum;
    }

    public function getQueerCountForAgeRange(AgeRange $ageRange): int
    {
        $sum = 0;
        foreach ($this->getAgeGroups() as $ageGroup) {
            if (!$ageGroup->getGender()->isQueer()) {
                continue;
            }
            if (!$ageGroup->getAgeRange()->equal($ageRange)) {
                continue;
            }
            $sum += $ageGroup->getPeopleCount()->getCount();
        }

        return $sum;
    }

    #[Groups(['wayPoint:read'])]
    public function getIsMeeting(): bool
    {
        return $this->isMeeting;
    }

    public function setIsMeeting(bool $isMeeting): void
    {
        $this->isMeeting = $isMeeting;
    }

    #[Groups(['wayPoint:read'])]
    public function getLocationName(): string
    {
        return $this->locationName;
    }

    public function setLocationName(?string $locationName): void
    {
        $this->locationName = \strip_tags((string) $locationName);
    }

    #[Groups(['wayPoint:read'])]
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    #[Groups(['wayPoint:read'])]
    public function getImageSrc(): string
    {
        return $this->imageSrc;
    }

    public function setImageSrc(string $imageSrc): void
    {
        $this->imageSrc = $imageSrc;
    }

    public function setImageName(string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function unsetImageName(): void
    {
        $this->imageName = null;
    }

    #[Groups(['wayPoint:read'])]
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    #[Groups(['wayPoint:read'])]
    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    #[Groups(['wayPoint:read'])]
    public function getVisitedAt(): \DateTimeInterface
    {
        return $this->visitedAt;
    }

    public function setVisitedAt(\DateTimeInterface $visitedAt): void
    {
        $this->visitedAt = $visitedAt;
    }

    #[Groups(['wayPoint:read'])]
    public function getOneOnOneInterview(): string
    {
        return $this->oneOnOneInterview;
    }

    public function setOneOnOneInterview(string $oneOnOneInterview): void
    {
        $this->oneOnOneInterview = \trim($oneOnOneInterview);
    }

    #[Groups(['wayPoint:read'])]
    public function getWalk(): Walk
    {
        return $this->walk;
    }

    public function setWalk(Walk $walk): void
    {
        $this->walk = $walk;
    }

    /**
     * @return Collection<int, Tag>
     */
    #[Groups(['wayPoint:read'])]
    public function getWayPointTags(): Collection
    {
        return $this->wayPointTags;
    }

    /**
     * @param Collection<int, Tag> $wayPointTags
     */
    public function setWayPointTags(Collection $wayPointTags): void
    {
        /** @var Tag $wayPointTag */
        foreach ($this->wayPointTags as $wayPointTag) {
            $wayPointTag->removeWayPoint($this);
        }
        $this->wayPointTags = $wayPointTags;
        /** @var Tag $wayPointTag */
        foreach ($this->wayPointTags as $wayPointTag) {
            $wayPointTag->addWayPoint($this);
        }
    }

    public function addWayPointTag(Tag $tag): void
    {
        $tag->addWayPoint($this);
        $this->wayPointTags->add($tag);
    }

    public function removeWayPointTag(Tag $tag): void
    {
        $tag->removeWayPoint($this);
        $this->wayPointTags->removeElement($tag);
    }

    #[Groups(['wayPoint:read'])]
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    #[Groups(['wayPoint:read'])]
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function __toString(): string
    {
        return \sprintf(
            '%s',
            $this->getLocationName()
        );
    }

    #[Groups(['wayPoint:read'])]
    public function getContactsCount(): ?int
    {
        return $this->contactsCount;
    }

    public function setContactsCount(?int $contactsCount): void
    {
        $this->contactsCount = $contactsCount;
    }
}
