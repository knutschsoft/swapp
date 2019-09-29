<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctrineORMSystemicQuestionRepository")
 * @ORM\Table(name="systemic_question")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class SystemicQuestion
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
     * @ORM\Column(type="string", length=4096)
     *
     * @var string
     */
    private $question;

    /**
     * @ORM\OneToMany(targetEntity="Walk", mappedBy="systemicQuestion")
     *
     * @var Walk[]|Collection
     */
    private $walks;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     *
     * @var ?\DateTime
     */
    private $deletedAt;

    public function __construct()
    {
        $this->walks = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->question;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

    /**
     * @return Walk[]|Collection
     */
    public function getWalks()
    {
        return $this->walks;
    }

    /**
     * @param Collection|Walk[] $walks
     */
    public function setWalks($walks): void
    {
        $this->walks = $walks;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function addWalk(Walk $walk): SystemicQuestion
    {
        $this->walks[] = $walk;

        return $this;
    }

    public function removeWalk(Walk $walk): void
    {
        $this->walks->removeElement($walk);
    }
}
