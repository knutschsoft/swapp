<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DoctrineORMSystemicQuestionRepository")
 * @ORM\Table(name="systemic_question")
 */
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: [
    'get' => [
        'security' => 'is_granted("SYSTEMIC_QUESTION_READ", object)',
    ],
    ],
    normalizationContext: ["groups" => ["systemicQuestion:read"]]
)]
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

    /** @ORM\ManyToOne(targetEntity="Client", inversedBy="systemicQuestions") */
    private Client $client;

    private function __construct(string $question, Client $client)
    {
        Assert::minLength($question, 5);
        Assert::maxLength($question, 4096);
        $this->question = $question;
        $this->client = $client;
    }

    public static function fromString(string $question, Client $client): self
    {
        return new self($question, $client);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @Groups({"systemicQuestion:read"})
     *
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

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
        return $this->question;
    }
}
