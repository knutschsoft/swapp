<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineORMTeamRepository")
 * @ORM\Table(name="team")
 */
class Team
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var arrayCollection
     * @ORM\ManyToMany(
     *     targetEntity="AppBundle\Entity\User",
     *     mappedBy="teams")
     */
    public $users;

    /**
     * Team constructor.
     */
    public function __construct()
    {
        $this->users= new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User $user
     */
    public function addUser($user)
    {
        $this->users[] = $user;
        $user->addTeam($this);
    }

    /**
     * @param User $user
     */
    public function removeUser($user)
    {
        $this->users->removeElement($user);
        $user->removeTeam($this);
    }

    /**
     * @param ArrayCollection
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }
}
