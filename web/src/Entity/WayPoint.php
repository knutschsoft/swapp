<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Dto\WayPointAddRequest;
use App\Value\AgeGroup;
use App\Value\AgeRange;
use App\Value\Gender;
use App\Value\PeopleCount;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctrineORMWayPointRepository")
 * @ORM\Table(name="way_point")
 */
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
    normalizationContext: ["groups" => ["wayPoint:read"]]
)]
class WayPoint
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, name="image_name", nullable=true)
     *
     * @var ?string
     */
    private $imageName;

    /**
     * @ORM\ManyToOne(targetEntity="Walk", inversedBy="wayPoints")
     *
     * @var Walk
     *
     * @MaxDepth(2)
     */
    private $walk;

    /**
     * @ORM\Column(type="string", length=4096)
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $locationName;

    /**
     * @ORM\Column(type="json_document", options={"jsonb": true})
     *
     * @var AgeGroup[]
     */
    private $ageGroups;
    /**
     * @ORM\Column(type="string", nullable=true, length=4096)
     *
     * @var ?string
     */
    private $note;

    /**
     * @ORM\Column(type="boolean", length=255)
     *
     * @var bool
     */
    private $isMeeting;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="wayPoints")
     *
     * @var Collection|Tag[]
     */
    private $wayPointTags;

    public function __construct()
    {
        $this->wayPointTags = new ArrayCollection();
        $this->ageGroups = [];
        $this->locationName = '';
        $this->isMeeting = false;
        $this->note = '';
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

    public static function fromWayPointAddRequest(WayPointAddRequest $request): self
    {
        $instance = new self();

        $instance->setWalk($request->walk);
        $instance->ageGroups = $request->ageGroups;
        $instance->wayPointTags = $request->tags;
        $instance->setNote($request->note);
        $instance->setLocationName($request->locationName);
        if ($request->imageFileName) {
            $instance->setImageName($request->imageFileName);
        }

        return $instance;
    }

    /**
     * @return AgeGroup[]
     *
     * @Groups({"wayPoint:read", "walk:read"})
     */
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

    /**
     * @return int
     *
     * @Groups({"wayPoint:read", "walk:read"})
     */
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

    /**
     * @return int
     *
     * @Groups({"wayPoint:read", "walk:read"})
     */
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

    /**
     * @return int
     *
     * @Groups({"wayPoint:read", "walk:read"})
     */
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
            if (!$ageGroup->ageRange->equal($ageRange)) {
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
            if (!$ageGroup->ageRange->equal($ageRange)) {
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
            if (!$ageGroup->ageRange->equal($ageRange)) {
                continue;
            }
            $sum += $ageGroup->getPeopleCount()->getCount();
        }

        return $sum;
    }

    /**
     * @return bool
     *
     * @Groups({"wayPoint:read", "walk:read"})
     */
    public function getIsMeeting(): bool
    {
        return $this->isMeeting;
    }

    public function setIsMeeting(bool $isMeeting): void
    {
        $this->isMeeting = $isMeeting;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf(
            '%s',
            $this->getLocationName()
        );
    }

    /**
     * @return string
     *
     * @Groups({"wayPoint:read", "walk:read"})
     */
    public function getLocationName(): string
    {
        return $this->locationName;
    }

    public function setLocationName(?string $locationName): void
    {
        $this->locationName = (string) $locationName;
    }

    /**
     * @return string|null
     *
     * @Groups({"wayPoint:read", "walk:read"})
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): void
    {
        $this->imageName = $imageName;
    }

    /**
     * @return int
     *
     * @Groups({"wayPoint:read", "walk:read"})
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     *
     * @Groups({"wayPoint:read", "walk:read"})
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    public function getWalk(): Walk
    {
        return $this->walk;
    }

    public function setWalk(Walk $walk): void
    {
        $this->walk = $walk;
    }

    /**
     * @return Collection|Tag[]
     *
     * @Groups({"wayPoint:read", "walk:read"})
     */
    public function getWayPointTags()
    {
        return $this->wayPointTags;
    }

    /**
     * @param Collection|Tag[] $wayPointTags
     */
    public function setWayPointTags($wayPointTags): void
    {
        $this->wayPointTags = $wayPointTags;
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
}