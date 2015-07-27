<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineORMWalkUserRepository")
 * @ORM\Table(name="walk_user")
 **/
class WalkUser
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
    protected $user_id;

    /**
     * @param integer $id
     */
    public function setUserId($id)
    {
        $this->user_id = $id;
    }

    /**
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
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
