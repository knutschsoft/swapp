<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Dto\TagCreateRequest;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctrineORMTagRepository")
 * @ORM\Table(name="tag")
 */
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
        "security" => 'is_granted("ROLE_SUPER_ADMIN")',
    ],
    ],
    itemOperations: ['get'],
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
    private string $name;

    /**
     * @ORM\ManyToMany(targetEntity="Walk", inversedBy="walkTags")
     *
     * @var Collection<int, Walk>
     */
    private Collection $walks;

    /**
     * @ORM\ManyToMany(targetEntity="WayPoint", inversedBy="wayPointTags")
     *
     * @var Collection<int, WayPoint>
     */
    private Collection $wayPoints;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private string $color;

    public function __construct()
    {
        $this->wayPoints = new ArrayCollection();
        $this->walks = new ArrayCollection();
        $this->color = '';
        $this->name = '';
    }

    public static function fromTagCreateRequest(TagCreateRequest $request): self
    {
        $instance = new self();

        $color = \trim($request->color);
        \Webmozart\Assert\Assert::inArray($request->color, self::COLORS);
        $instance->color = $color;

        $name = \trim($request->name);
        \Webmozart\Assert\Assert::minLength($name, 3);
        \Webmozart\Assert\Assert::maxLength($name, 100);
        $instance->name = $name;

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

    public function __toString(): string
    {
        return \sprintf(
            '%s (%s)',
            $this->getName(),
            $this->getColor()
        );
    }
}
