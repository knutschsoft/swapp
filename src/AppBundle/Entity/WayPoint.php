<?php
namespace AppBundle\Entity;

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
    protected $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="way_point_image", fileNameProperty="imageName")
     *
     * @var File $imageFile
     */
    protected $imageFile;

    /**
     * @ORM\Column(type="string", length=255, name="image_name", nullable=true)
     *
     * @var string $imageName
     */
    protected $imageName;

    /**
     * @ORM\ManyToOne(targetEntity="Walk", inversedBy="wayPoints")
     */
    protected $walk;

    /**
     * @ORM\Column(type="string", length=4096)
     * @Assert\NotBlank()
     */
    protected $locationName;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    protected $ageRangeStart;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    protected $ageRangeEnd;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    protected $malesCount;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    protected $femalesCount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $note;

    /**
     * @ORM\Column(type="boolean", length=255)
     */
    protected $isMeeting;

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
            '%s (%s-%s)',
            $this->getLocationName(),
            $this->getAgeRangeStart(),
            $this->getAgeRangeEnd()
        );
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
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * @param mixed $ageRangeEnd
     */
    public function setAgeRangeEnd($ageRangeEnd)
    {
        $this->ageRangeEnd = $ageRangeEnd;
    }

    /**
     * @return mixed
     */
    public function getAgeRangeEnd()
    {
        return $this->ageRangeEnd;
    }

    /**
     * @param mixed $ageRangeStart
     */
    public function setAgeRangeStart($ageRangeStart)
    {
        $this->ageRangeStart = $ageRangeStart;
    }

    /**
     * @return mixed
     */
    public function getAgeRangeStart()
    {
        return $this->ageRangeStart;
    }

    /**
     * @param mixed $femalesCount
     */
    public function setFemalesCount($femalesCount)
    {
        $this->femalesCount = $femalesCount;
    }

    /**
     * @return mixed
     */
    public function getFemalesCount()
    {
        return $this->femalesCount;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $locationName
     */
    public function setLocationName($locationName)
    {
        $this->locationName = $locationName;
    }

    /**
     * @return mixed
     */
    public function getLocationName()
    {
        return $this->locationName;
    }

    /**
     * @param mixed $malesCount
     */
    public function setMalesCount($malesCount)
    {
        $this->malesCount = $malesCount;
    }

    /**
     * @return mixed
     */
    public function getMalesCount()
    {
        return $this->malesCount;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $walk
     */
    public function setWalk($walk)
    {
        $this->walk = $walk;
    }

    /**
     * @return mixed
     */
    public function getWalk()
    {
        return $this->walk;
    }
}
