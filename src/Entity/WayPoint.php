<?php
declare(strict_types=1);

namespace App\Entity;

use App\Value\AgeGroup;
use App\Value\Gender;
use App\Value\PeopleCount;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctrineORMWayPointRepository")
 * @ORM\Table(name="way_point")
 *
 * @Vich\Uploadable
 */
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
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="way_point_image", fileNameProperty="imageName")
     *
     * @Assert\File(maxSize = "10240k")
     *
     * @var File|null
     */
    private $imageFile;

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
    }

    public static function fromWalk(Walk $walk): self
    {
        $instance = new self();

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

    /**
     * @return AgeGroup[]
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

    public function getFemalesCount(): int
    {
        $sum = 0;
        foreach ($this->getAgeGroups() as $ageGroup) {
            if (!$ageGroup->gender()->isFemale()) {
                continue;
            }
            $sum += $ageGroup->peopleCount()->count();
        }

        return $sum;
    }

    public function getMalesCount(): int
    {
        $sum = 0;
        foreach ($this->getAgeGroups() as $ageGroup) {
            if (!$ageGroup->gender()->isMale()) {
                continue;
            }
            $sum += $ageGroup->peopleCount()->count();
        }

        return $sum;
    }

    public function getQueerCount(): int
    {
        $sum = 0;
        foreach ($this->getAgeGroups() as $ageGroup) {
            if (!$ageGroup->gender()->isQueer()) {
                continue;
            }
            $sum += $ageGroup->peopleCount()->count();
        }

        return $sum;
    }

    /**
     * @return bool
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

    public function getLocationName(): string
    {
        return $this->locationName;
    }

    public function setLocationName(?string $locationName): void
    {
        $this->locationName = (string) $locationName;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageFile(?File $image = null): void
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

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
