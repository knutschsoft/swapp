<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This is for testing doctrine only, will be replaced with real user entity
 * @UniqueEntity(fields="email", message="Email already taken")
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @package AppBundle\Entity
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Walk", inversedBy="walkTeamMembers")
     */
    protected $walks;

    /**
     * @ORM\ManyToMany(targetEntity="Team", mappedBy="users")
     */
    protected $teams;

    /**
     * @return Team[]
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @param Team[] $teams
     */
    public function setTeams($teams)
    {
        $this->teams = $teams;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '%s (%s)',
            $this->getUsername(),
            $this->getEmail()
        );
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
     * @param mixed $walks
     */
    public function setWalks($walks)
    {
        $this->walks = $walks;
    }

    /**
     * @return mixed
     */
    public function getWalks()
    {
        return $this->walks;
    }
}
