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
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="Walk", inversedBy="guests")
     *
     * @var Walk
     */
    private $walk;

    public function __toString(): string
    {
        return \sprintf(
            "%s (%s)",
            $this->getName(),
            $this->getEmail()
        );
    }

    public function getWalk(): Walk
    {
        return $this->walk;
    }

    public function setWalk(Walk $walk): void
    {
        $this->walk = $walk;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
