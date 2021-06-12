<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Dto\Client\ClientChangeRequest;
use App\Dto\Client\ClientInitRequest;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctrineORMClientRepository")
 * @ORM\Table(name="client")
 */
#[ApiResource(
    collectionOperations: [
    "get",
    "change_client" => [
        "messenger" => "input",
        "input" => ClientChangeRequest::class,
        "output" => Client::class,
        "method" => "post",
        "status" => 200,
        "path" => "/clients/change",
    ],
    "add_client" => [
        "messenger" => "input",
        "input" => ClientInitRequest::class,
        "output" => Client::class,
        "method" => "post",
        "status" => 200,
        "path" => "/clients/init",
    ],
    ],
    itemOperations: [
    'get' => [
        'security' => 'is_granted("CLIENT_READ", object)',
    ],
    ],
)]
class Client
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /** @ORM\Column(type="string", length=255) */
    private string $name;

    /** @ORM\Column(type="string", length=255) */
    private string $email;

    /** @ORM\Column(type="text") */
    private string $description;

    /**
     * @ORM\OneToMany(targetEntity="SystemicQuestion", mappedBy="client")
     *
     * @var Collection<int, SystemicQuestion>
     **/
    private Collection $systemicQuestions;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="client")
     *
     * @var Collection<int, User>
     **/
    private Collection $users;

    /**
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="client")
     *
     * @var Collection<int, Tag>
     **/
    private Collection $tags;

    /**
     * @ORM\OneToMany(targetEntity="Team", mappedBy="client")
     *
     * @var Collection<int, Team>
     **/
    private Collection $teams;

    /**
     * @ORM\OneToMany(targetEntity="Walk", mappedBy="client")
     *
     * @var Collection<int, Walk>
     **/
    private Collection $walks;

    public function __construct()
    {
        $this->systemicQuestions = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->walks = new ArrayCollection();
    }

    public static function fromClientInitRequest(ClientInitRequest $request): self
    {
        $instance = new self();
        $instance->name = \trim($request->name);
        $instance->email = \trim($request->email);
        $instance->description = \trim($request->description);

        return $instance;
    }

    public function updateByClientChangeRequest(ClientChangeRequest $request): void
    {
        $this->name = \trim($request->name);
        $this->email = \trim($request->email);
        $this->description = \trim($request->description);
        $this->users = new ArrayCollection($request->users);
        $this->teams = new ArrayCollection($request->teams);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function updateEmail(string $email): void
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function updateDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection<int, SystemicQuestion>
     */
    public function getSystemicQuestions(): Collection
    {
        return $this->systemicQuestions;
    }

    public function addSystemicQuestion(SystemicQuestion $systemicQuestion): void
    {
        $systemicQuestion->updateClient($this);
        $this->systemicQuestions->add($systemicQuestion);
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): void
    {
        $user->updateClient($this);
        $this->users->add($user);
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): void
    {
        $tag->updateClient($this);
        $this->tags->add($tag);
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): void
    {
        $team->updateClient($this);
        $this->teams->add($team);
    }

    /**
     * @return Collection<int, Walk>
     */
    public function getWalks(): Collection
    {
        return $this->walks;
    }

    public function addWalk(Walk $walk): void
    {
        $walk->updateClient($this);
        $this->walks->add($walk);
    }
}
