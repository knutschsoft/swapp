<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\DoctrineORMGuestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'guest')]
#[ORM\Entity(repositoryClass: DoctrineORMGuestRepository::class)]
class Guest
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue()]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $email;

    #[ORM\ManyToOne(targetEntity: Walk::class, inversedBy: 'guests')]
    private Walk $walk;

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

    public function __toString(): string
    {
        return \sprintf(
            '%s (%s)',
            $this->getName(),
            $this->getEmail()
        );
    }
}
