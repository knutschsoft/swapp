<?php

namespace AppBundle\Entity;

use AppBundle\Value\AgeGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineORMWayPointRepository")
 * @ORM\Table(name="way_point")
 * @Vich\Uploadable
 */
class WayPoint
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="way_point_image", fileNameProperty="imageName")
     *
     * @Assert\File(maxSize = "10240k")
     *
     * @var File $imageFile
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, name="image_name", nullable=true)
     *
     * @var string $imageName
     */
    private $imageName;

    /**
     * @ORM\ManyToOne(targetEntity="Walk", inversedBy="wayPoints")
     */
    private $walk;

    /**
     * @ORM\Column(type="string", length=4096)
     * @Assert\NotBlank()
     */
    private $locationName;

    /**
     * @ORM\Column(type="json_document", options={"jsonb": true})
     * @var AgeGroup[]
     */
    private $ageGroups;
    /**
     * @ORM\Column(type="string", nullable=true, length=4096)
     */
    private $note;

    /**
     * @ORM\Column(type="boolean", length=255)
     */
    private $isMeeting;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="wayPoints")
     * @var ArrayCollection
     */
    private $wayPointTags;

    public function __construct()
    {
        $this->wayPointTags = new ArrayCollection();
        $this->ageGroups = [];
    }

    /**
     * @return AgeGroup[]
     */
    public function getAgeGroups(): array
    {
        return $this->ageGroups;
    }

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
     * @return boolean
     */
    public function getIsMeeting()
    {
        return $this->isMeeting;
    }

    /**
     * @param boolean $isMeeting
     */
    public function setIsMeeting($isMeeting)
    {
        $this->isMeeting = $isMeeting;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '%s',
            $this->getLocationName()
        );
    }

    /**
     * @return string
     */
    public function getLocationName()
    {
        return $this->locationName;
    }

    /**
     * @param string $locationName
     */
    public function setLocationName($locationName)
    {
        $this->locationName = $locationName;
    }

    /**
     * @return File
     */
    public function getImageFile()
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
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return Walk
     */
    public function getWalk()
    {
        return $this->walk;
    }

    /**
     * @param Walk $walk
     */
    public function setWalk(Walk $walk)
    {
        $this->walk = $walk;
    }

    /**
     * @return mixed
     */
    public function getWayPointTags()
    {
        return $this->wayPointTags;
    }

    /**
     * @param mixed $wayPointTags
     */
    public function setWayPointTags($wayPointTags)
    {
        $this->wayPointTags = $wayPointTags;
    }

    /**
     * @param Tag $tag
     */
    public function addWayPointTag(Tag $tag)
    {
        $tag->addWayPoint($this);
        $this->wayPointTags->add($tag);
    }

    /**
     * @param Tag $tag
     */
    public function removeWayPointTag(Tag $tag)
    {
        $tag->removeWayPoint($this);
        $this->wayPointTags->removeElement($tag);
    }
}
