<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Dto\WayPoint\WayPointChangeRequest;
use App\Dto\WayPointCreateRequest;
use App\Repository\DoctrineORMWayPointRepository;
use App\Value\AgeGroup;
use App\Value\AgeRange;
use App\Value\Gender;
use App\Value\PeopleCount;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Table(name: 'way_point')]
#[ORM\Entity(repositoryClass: DoctrineORMWayPointRepository::class)]
#[ApiFilter(OrderFilter::class, properties: ["walk.updatedAt", "locationName", "oneOnOneInterview", "note", "walk.startTime", "walk.teamName"])]
#[ApiFilter(BooleanFilter::class, properties: ["isMeeting"])]
#[ApiFilter(
    SearchFilter::class,
    properties: ['locationName' => 'partial', 'note' => 'partial', 'oneOnOneInterview' => 'partial', 'wayPointTags' => 'exact', 'walk.teamName' => 'partial']
)]
#[ApiResource(
    collectionOperations: [
    'get',
    "way_point_change" => [
        "messenger" => "input",
        "input" => WayPointChangeRequest::class,
        "output" => WayPoint::class,
        "method" => "post",
        "status" => 200,
        "path" => "/way_points/change",
    ],
    "way_point_create" => [
        "messenger" => "input",
        "input" => WayPointCreateRequest::class,
        "output" => WayPoint::class,
        "method" => "post",
        "status" => 200,
        "path" => "/way_points/create",
    ],
    ],
    itemOperations: ['get'],
    attributes: [
        "order" => ["locationName" => "ASC"],
    ],
    normalizationContext: ["groups" => ["wayPoint:read"]],
)]
class WayPoint
{
    use TimestampableEntity;

    /** @Gedmo\Timestampable(on="create") **/
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected $createdAt; // phpcs:ignore

    /** @Gedmo\Timestampable(on="create") **/
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected $updatedAt; // phpcs:ignore

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue()]
    private int $id;

    #[ORM\Column(name: 'image_name', type: 'string', length: 255, nullable: true)]
    private ?string $imageName = null;

    /** @MaxDepth(2) */
    #[ORM\ManyToOne(targetEntity: Walk::class, inversedBy: 'wayPoints')]
    private Walk $walk;

    #[ORM\Column(type: 'string', length: 4096)]
    private string $locationName;

    /** @var AgeGroup[] */
    #[ORM\Column(type: 'json_document')]
    private array $ageGroups;

    #[ORM\Column(type: 'string', length: 4096, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(type: 'string', length: 4096)]
    private string $oneOnOneInterview;

    #[ORM\Column(type: 'boolean')]
    private bool $isMeeting;

    /** @var Collection<int, Tag> */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'wayPoints')]
    private Collection $wayPointTags;

    public function __construct()
    {
        $this->wayPointTags = new ArrayCollection();
        $this->ageGroups = [];
        $this->locationName = '';
        $this->isMeeting = false;
        $this->note = '';
        $this->oneOnOneInterview = '';
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
        $instance->ageGroups = $request->ageGroups;
        $instance->wayPointTags = new ArrayCollection($request->tags);
        $instance->setNote($request->note);
        $instance->setOneOnOneInterview($request->oneOnOneInterview);
        $instance->setLocationName($request->locationName);
        if ($request->imageFileName) {
            $instance->setImageName($request->imageFileName);
        }
        $instance->setIsMeeting($request->isMeeting);

        return $instance;
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
        $this->locationName = (string) $locationName;
    }

    #[Groups(['wayPoint:read'])]
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): void
    {
        $this->imageName = $imageName;
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
}
