<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctrineORMGuestRepository")
 * @ORM\Table(name="guest")
 */
class Guest
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $email;

    /** @ORM\ManyToOne(targetEntity="Walk", inversedBy="guests") */
    private $walk;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf(
            "%s (%s)",
            $this->getName(),
            $this->getEmail()
        );
    }

    /**
     * @return Walk
     */
    public function getWalk(): Walk
    {
        return $this->walk;
    }

    /**
     * @param Walk $walk
     */
    public function setWalk(Walk $walk): void
    {
        $this->walk = $walk;
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
    public function setId($id): void
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
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }
}
