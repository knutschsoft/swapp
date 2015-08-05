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
    private $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="way_point_image", fileNameProperty="imageName")
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
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    private $malesChildCount;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    private $femalesChildCount;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    private $malesKidCount;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    private $femalesKidCount;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    private $malesYouthCount;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    private $femalesYouthCount;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    private $malesAdultCount;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    private $femalesAdultCount;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $note;
    /**
     * @ORM\Column(type="boolean", length=255)
     */
    private $isMeeting;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="wayPoints")
     */
    private $tags;

    /**
     * @return mixed
     */
    public function getMalesKidCount()
    {
        return $this->malesKidCount;
    }

    /**
     * @param mixed $malesKidCount
     */
    public function setMalesKidCount($malesKidCount)
    {
        $this->malesKidCount = $malesKidCount;
    }

    /**
     * @return mixed
     */
    public function getFemalesKidCount()
    {
        return $this->femalesKidCount;
    }

    /**
     * @param mixed $femalesKidCount
     */
    public function setFemalesKidCount($femalesKidCount)
    {
        $this->femalesKidCount = $femalesKidCount;
    }

    /**
     * @return mixed
     */
    public function getMalesYouthCount()
    {
        return $this->malesYouthCount;
    }

    /**
     * @param mixed $malesYouthCount
     */
    public function setMalesYouthCount($malesYouthCount)
    {
        $this->malesYouthCount = $malesYouthCount;
    }

    /**
     * @return mixed
     */
    public function getFemalesYouthCount()
    {
        return $this->femalesYouthCount;
    }

    /**
     * @param mixed $femalesYouthCount
     */
    public function setFemalesYouthCount($femalesYouthCount)
    {
        $this->femalesYouthCount = $femalesYouthCount;
    }

    /**
     * @return mixed
     */
    public function getMalesAdultCount()
    {
        return $this->malesAdultCount;
    }

    /**
     * @param mixed $malesAdultCount
     */
    public function setMalesAdultCount($malesAdultCount)
    {
        $this->malesAdultCount = $malesAdultCount;
    }

    /**
     * @return mixed
     */
    public function getFemalesAdultCount()
    {
        return $this->femalesAdultCount;
    }

    /**
     * @param mixed $femalesAdultCount
     */
    public function setFemalesAdultCount($femalesAdultCount)
    {
        $this->femalesAdultCount = $femalesAdultCount;
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
            '%s (%s-%s)',
            $this->getLocationName()
        );
    }

    /**
     * @return mixed
     */
    public function getLocationName()
    {
        return $this->locationName;
    }

    /**
     * @param mixed $locationName
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
     * @return mixed
     */
    public function getFemalesChildCount()
    {
        return $this->femalesChildCount;
    }

    /**
     * @param mixed $femalesChildCount
     */
    public function setFemalesChildCount($femalesChildCount)
    {
        $this->femalesChildCount = $femalesChildCount;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
    public function getMalesChildCount()
    {
        return $this->malesChildCount;
    }

    /**
     * @param mixed $malesChildCount
     */
    public function setMalesChildCount($malesChildCount)
    {
        $this->malesChildCount = $malesChildCount;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
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
    public function getWalk()
    {
        return $this->walk;
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
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }
}
