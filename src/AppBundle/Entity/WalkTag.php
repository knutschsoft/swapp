<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineORMWalkTagRepository")
 * @ORM\Table(name="walk_tag")
 **/
class WalkTag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $walk_id;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $tag_id;

    /**
     * @param integer $id
     */
    public function setTagId($id)
    {
        $this->tag_id = $id;
    }

    /**
     * @return integer
     */
    public function getTagId()
    {
        return $this->tag_id;
    }

    /**
     * @param integer $id
     */
    public function setWalkId($id)
    {
        $this->walk_id = $id;
    }

    /**
     * @return integer
     */
    public function getWalkId()
    {
        return $this->walk_id;
    }
}
