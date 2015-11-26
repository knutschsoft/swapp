<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineORMSystemicQuestionRepository")
 * @ORM\Table(name="systemic_question")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class SystemicQuestion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     **/
    private $question;

    /**
     * @ORM\OneToMany(targetEntity="Walk", mappedBy="systemicQuestion")
     **/
    private $walks;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    public function __construct()
    {
        $this->walks = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->question;
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
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return Walk[]
     */
    public function getWalks()
    {
        return $this->walks;
    }

    /**
     * @param Walk[] $walks
     */
    public function setWalks($walks)
    {
        $this->walks = $walks;
    }

    /**
     * @return null|string
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param null|string $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }
}
