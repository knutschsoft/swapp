<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Dto\TagCreateRequest;
use App\Repository\DoctrineORMTagRepository;
use App\Security\Voter\ClientVoter;
use App\Security\Voter\TagVoter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Webmozart\Assert\Assert;

#[ORM\Table(name: 'tag')]
#[ORM\Entity(repositoryClass: DoctrineORMTagRepository::class)]
#[ApiResource(
    collectionOperations: [
    'get',
    'tag_create' => [
        "messenger" => "input",
        "input" => TagCreateRequest::class,
        "output" => Tag::class,
        "method" => "post",
        "status" => 200,
        "path" => "/tags/create",
        "security_post_denormalize" => 'is_granted("'.User::ROLE_ADMIN.'") && is_granted("'.ClientVoter::READ.'", object.client)',
    ],
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted("'.TagVoter::READ.'", object)',
        ],
    ],
    normalizationContext: ["groups" => ["tag:read"]]
)]
class Tag
{
    public const COLORS = [
        "Navy",
        "MediumBlue",
        "Blue",
        "Indigo ",
        "Maroon",
        "DarkSlateBlue",
        "Lime",
        "Purple",
        "SaddleBrown",
        "DarkTurquoise",
        "Brown",
        "RoyalBlue",
        "FireBrick",
        "DeepSkyBlue",
        "Olive",
        "SteelBlue",
        "SlateBlue",
        "Crimson",
        "CadetBlue",
        "DarkOrchid",
        "Aqua",
        "MediumSlateBlue",
        "CornflowerBlue",
        "Turquoise",
        "Chocolate",
        "MediumAquaMarine",
        "Chartreuse",
        "MediumOrchid",
        "Peru",
        "DeepPink",
        "RosyBrown",
        "GoldenRod",
        "Fuchsia",
        "Tomato",
        "DarkOrange",
        "SkyBlue",
        "Orchid",
        "Orange",
        "Coral",
        "Aquamarine",
        "Salmon",
        "DarkSalmon",
        "Tan",
        "LightSteelBlue",
        "Silver",
        "SandyBrown",
        "Gold",
        "Plum",
        "PowderBlue",
        "LightSalmon",
        "Thistle",
        "Yellow",
        "Khaki",
        "LightPink",
        "PaleGoldenRod",
        "Wheat",
        "Lavender",
        "Bisque",
        "Beige",
        "MistyRose",
        "Linen",
        "LemonChiffon",
        "Azure",
        "LavenderBlush",
        "Ivory",
    ];

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue()]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    /** @var Collection<int, Walk> */
    #[ORM\ManyToMany(targetEntity: Walk::class, inversedBy: 'walkTags')]
    private Collection $walks;

    /** @var Collection<int, WayPoint> */
    #[ORM\ManyToMany(targetEntity: WayPoint::class, inversedBy: 'wayPointTags')]
    private Collection $wayPoints;

    #[ORM\Column(type: 'string', length: 255)]
    private string $color;

    public function __construct()
    {
        $this->wayPoints = new ArrayCollection();
        $this->walks = new ArrayCollection();
        $this->color = '';
        $this->name = '';
    }

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'tags')]
    private Client $client;

    public static function fromTagCreateRequest(TagCreateRequest $request): self
    {
        $instance = new self();

        $color = \trim($request->color);
        Assert::inArray($request->color, self::COLORS);
        $instance->color = $color;

        $name = \trim($request->name);
        Assert::minLength($name, 3);
        Assert::maxLength($name, 100);
        $instance->name = $name;
        $instance->client = $request->client;

        return $instance;
    }

    /**
     * @return string
     *
     * @Groups({"tag:read", "walk:read"})
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
     * @return string
     *
     * @Groups({"tag:read", "walk:read"})
     */
    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @Groups({"tag:read", "walk:read"})
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function addWalk(Walk $walk): void
    {
        if (!$this->walks->contains($walk)) {
            $this->walks->add($walk);
        }
    }

    public function removeWalk(Walk $walk): void
    {
        if ($this->walks->contains($walk)) {
            $this->walks->removeElement($walk);
        }
    }

    /** @return Collection<int, Walk> */
    public function getWalks(): Collection
    {
        return $this->walks;
    }

    /** @param Collection<int, Walk> $walks */
    public function setWalks(Collection $walks): void
    {
        $this->walks = $walks;
    }

    public function addWayPoint(WayPoint $wayPoint): void
    {
        if (!$this->wayPoints->contains($wayPoint)) {
            $this->wayPoints->add($wayPoint);
        }
    }

    public function removeWayPoint(WayPoint $wayPoint): void
    {
        if ($this->wayPoints->contains($wayPoint)) {
            $this->wayPoints->removeElement($wayPoint);
        }
    }

    /** @return Collection<int, WayPoint> */
    public function getWayPoints(): Collection
    {
        return $this->wayPoints;
    }

    /** @param Collection<int, WayPoint> $wayPoints */
    public function setWayPoints(Collection $wayPoints): void
    {
        $this->wayPoints = $wayPoints;
    }

    #[Groups(['tag:read'])]
    public function getClient(): Client
    {
        return $this->client;
    }

    public function updateClient(Client $client): void
    {
        $this->client = $client;
    }

    public function __toString(): string
    {
        return \sprintf(
            '%s (%s)',
            $this->getName(),
            $this->getColor()
        );
    }
}
