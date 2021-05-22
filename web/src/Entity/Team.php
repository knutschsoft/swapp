<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Fields\AgeRangeField;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctrineORMTeamRepository")
 * @ORM\Table(name="team")
 */
#[ApiResource(
    collectionOperations: ["get"],
    itemOperations: ["get"],
    normalizationContext: ["groups" => ["team:read"]]
)]
class Team
{
    use AgeRangeField;

    /**
     * @var Collection<int, User>
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Entity\User",
     *     mappedBy="teams")
     */
    private Collection $users;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private string $name = '';

    public function __construct()
    {
        $this->ageRanges = [];
        $this->users = new ArrayCollection();
    }

    /**
     * @return int
     *
     * @Groups({"user:read", "team:read"})
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     *
     * @Groups({"user:read", "team:read"})
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection<int, User>
     *
     * @Groups({"team:read"})
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addTeam($this);
        }
    }

    public function removeUser(User $user): void
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeTeam($this);
        }
    }

    /** @param Collection<int, User> $users */
    public function setUsers(Collection $users): void
    {
        $this->users = $users;
        foreach ($this->users as $user) {
            $user->removeTeam($this);
        }
        foreach ($users as $user) {
            $user->addTeam($this);
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
