<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Dto\Client\ClientChangeRequest;
use App\Dto\Client\ClientCreateRequest;
use App\Repository\DoctrineORMClientRepository;
use App\Security\Voter\ClientVoter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ApiResource(
    operations: [
        new Get(security: 'is_granted("'.ClientVoter::READ.'", object)'),
        new GetCollection(),
        new Post(
            uriTemplate: '/clients/change',
            status: 200,
            securityPostDenormalize: 'is_granted("'.ClientVoter::EDIT.'", object.client)',
            input: ClientChangeRequest::class,
            output: Client::class,
            messenger: 'input'
        ),
        new Post(
            uriTemplate: '/clients/create',
            status: 200,
            securityPostDenormalize: 'is_granted("'.ClientVoter::CREATE.'")',
            input: ClientCreateRequest::class,
            output: Client::class,
            messenger: 'input'
        ),
    ],
    normalizationContext: ['groups' => ['client:read']],
)]
#[ORM\Table(name: 'client')]
#[ORM\Entity(repositoryClass: DoctrineORMClientRepository::class)]
class Client
{
    use TimestampableEntity;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    protected $createdAt; // phpcs:ignore
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    protected $updatedAt; // phpcs:ignore

    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue()]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $email;

    #[ORM\Column(type: 'text')]
    private string $description;

    /** @var Collection<int, SystemicQuestion> **/
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: SystemicQuestion::class)]
    private Collection $systemicQuestions;

    /** @var Collection<int, User> **/
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: User::class)]
    private Collection $users;

    /** @var Collection<int, Tag> **/
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Tag::class)]
    private Collection $tags;

    /** @var Collection<int, Team> **/
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Team::class)]
    private Collection $teams;

    /** @var Collection<int, Walk> **/
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Walk::class)]
    private Collection $walks;

    #[ORM\Column(name: 'rating_image_name', type: 'string', length: 255, nullable: true)]
    private ?string $ratingImageName = null;
    private string $ratingImageSrc = '';

    public function __construct()
    {
        $this->systemicQuestions = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->walks = new ArrayCollection();
    }

    public static function fromClientInitRequest(ClientCreateRequest $request): self
    {
        $instance = new self();
        $instance->name = \trim($request->name);
        $instance->email = \trim($request->email);
        $instance->description = \trim($request->description);
        if ($request->ratingImageFileName) {
            $instance->setRatingImageName(\sprintf("%s_%s", \time(), $request->ratingImageFileName));
        }

        return $instance;
    }

    public function updateByClientChangeRequest(ClientChangeRequest $request): void
    {
        $this->name = \trim($request->name);
        $this->email = \trim($request->email);
        $this->description = \trim($request->description);
    }

    #[Groups(['client:read'])]
    #[SerializedName('clientId')]
    public function getId(): int
    {
        return $this->id;
    }

    #[Groups(['client:read'])]
    public function getName(): string
    {
        return $this->name;
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
    }

    #[Groups(['client:read'])]
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

    #[Groups(['client:read'])]
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
    #[Groups(['client:read'])]
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
    #[Groups(['client:read'])]
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

    #[Groups(['client:read'])]
    public function getRatingImageName(): ?string
    {
        return $this->ratingImageName;
    }

    #[Groups(['client:read'])]
    public function getRatingImageSrc(): string
    {
        return $this->ratingImageSrc;
    }

    public function setRatingImageSrc(string $ratingImageSrc): void
    {
        $this->ratingImageSrc = $ratingImageSrc;
    }

    public function setRatingImageName(string $ratingImageName): void
    {
        $this->ratingImageName = $ratingImageName;
    }

    public function unsetRatingImageName(): void
    {
        $this->ratingImageName = null;
    }

    #[Groups(['client:read'])]
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    #[Groups(['client:read'])]
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
}
