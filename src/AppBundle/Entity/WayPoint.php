<?php
namespace AppBundle\Entity;

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
    private $malesYoungAdultCount;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     */
    private $femalesYoungAdultCount;

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
    }

    /**
     * @return integer
     */
    public function getFemalesCount()
    {
        return $this->getFemalesKidCount() +
            $this->getFemalesChildCount() +
            $this->getFemalesAdultCount() +
            $this->getFemalesYoungAdultCount() +
            $this->getFemalesYouthCount();
    }

    /**
     * @return integer
     */
    public function getMalesCount()
    {
        return $this->getMalesKidCount() +
            $this->getMalesChildCount() +
            $this->getMalesAdultCount() +
            $this->getMalesYoungAdultCount() +
            $this->getMalesYouthCount();
    }

    /**
     * @return integer
     */
    public function getMalesKidCount()
    {
        return $this->malesKidCount;
    }

    /**
     * @param integer $malesKidCount
     */
    public function setMalesKidCount($malesKidCount)
    {
        $this->malesKidCount = $malesKidCount;
    }

    /**
     * @return integer
     */
    public function getFemalesKidCount()
    {
        return $this->femalesKidCount;
    }

    /**
     * @param integer $femalesKidCount
     */
    public function setFemalesKidCount($femalesKidCount)
    {
        $this->femalesKidCount = $femalesKidCount;
    }

    /**
     * @return integer
     */
    public function getMalesYouthCount()
    {
        return $this->malesYouthCount;
    }

    /**
     * @param integer $malesYouthCount
     */
    public function setMalesYouthCount($malesYouthCount)
    {
        $this->malesYouthCount = $malesYouthCount;
    }

    /**
     * @return integer
     */
    public function getFemalesYouthCount()
    {
        return $this->femalesYouthCount;
    }

    /**
     * @param integer $femalesYouthCount
     */
    public function setFemalesYouthCount($femalesYouthCount)
    {
        $this->femalesYouthCount = $femalesYouthCount;
    }

    /**
     * @return integer
     */
    public function getMalesAdultCount()
    {
        return $this->malesAdultCount;
    }

    /**
     * @param integer $malesAdultCount
     */
    public function setMalesAdultCount($malesAdultCount)
    {
        $this->malesAdultCount = $malesAdultCount;
    }

    /**
     * @return integer
     */
    public function getFemalesAdultCount()
    {
        return $this->femalesAdultCount;
    }

    /**
     * @param integer $femalesAdultCount
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
    public function getFemalesChildCount()
    {
        return $this->femalesChildCount;
    }

    /**
     * @param integer $femalesChildCount
     */
    public function setFemalesChildCount($femalesChildCount)
    {
        $this->femalesChildCount = $femalesChildCount;
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
     * @return integer
     */
    public function getMalesChildCount()
    {
        return $this->malesChildCount;
    }

    /**
     * @param integer $malesChildCount
     */
    public function setMalesChildCount($malesChildCount)
    {
        $this->malesChildCount = $malesChildCount;
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

    /**
     * @return integer
     */
    public function getMalesYoungAdultCount()
    {
        return $this->malesYoungAdultCount;
    }

    /**
     * @param integer $malesYoungAdultCount
     */
    public function setMalesYoungAdultCount($malesYoungAdultCount)
    {
        $this->malesYoungAdultCount = $malesYoungAdultCount;
    }

    /**
     * @return integer
     */
    public function getFemalesYoungAdultCount()
    {
        return $this->femalesYoungAdultCount;
    }

    /**
     * @param integer $femalesYoungAdultCount
     */
    public function setFemalesYoungAdultCount($femalesYoungAdultCount)
    {
        $this->femalesYoungAdultCount = $femalesYoungAdultCount;
    }

    public function getAllFemalesCount()
    {
        return $this->getFemalesKidCount()
            + $this->getFemalesChildCount()
            + $this->getFemalesYouthCount()
            + $this->getFemalesYoungAdultCount()
            + $this->getFemalesAdultCount();
    }

    public function getAllMalesCount()
    {
        return $this->getMalesKidCount()
            + $this->getMalesChildCount()
            + $this->getMalesYouthCount()
            + $this->getMalesYoungAdultCount()
            + $this->getMalesAdultCount();
    }
}
