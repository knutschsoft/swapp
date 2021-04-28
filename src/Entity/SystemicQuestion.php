<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctrineORMSystemicQuestionRepository")
 * @ORM\Table(name="systemic_question")
 */
class SystemicQuestion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /** @ORM\Column(type="string", length=4096) */
    private string $question = '';

    public static function fromString(string $question): self
    {
        Assert::minLength($question, 5);
        $instance = new self();
        $instance->setQuestion($question);

        return $instance;
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

    public function __toString(): string
    {
        return $this->question;
    }
}
