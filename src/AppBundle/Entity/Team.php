<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use AppBundle\Entity\Fields\AgeRangeField;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineORMTeamRepository")
 * @ORM\Table(name="team")
 */
class Team
{
    use AgeRangeField;

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
     * @var User[]|Collection
     * @ORM\ManyToMany(
     *     targetEntity="AppBundle\Entity\User",
     *     mappedBy="teams")
     */
    public $users;

    public function __construct()
    {
        $this->ageRanges = [];
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

    public function getUsers(): ArrayCollection
    {
        return $this->users;
    }

    public function addUser(User $user): void
    {
        $this->users[] = $user;
        $user->addTeam($this);
    }

    public function removeUser(User $user): void
    {
        $this->users->removeElement($user);
        $user->removeTeam($this);
    }

    /**
     * @param ArrayCollection $users
     */
    public function setUsers(ArrayCollection $users): void
    {
        $this->users = $users;
    }
}
