<?php
namespace AppBundle\Entity;

use AppBundle\Controller\WalksController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineORMSystemicQuestionRepository")
 * @ORM\Table(name="systemic_question")
 */
class SystemicQuestion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     **/
    protected $question;

    /**
     * @ORM\OneToMany(targetEntity="Walk", mappedBy="systemicQuestion")
     **/
    private $walks;

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
}
