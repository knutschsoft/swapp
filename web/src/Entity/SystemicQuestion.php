<?php
declare (strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Dto\SystemicQuestion\SystemicQuestionChangeRequest;
use App\Dto\SystemicQuestion\SystemicQuestionCreateRequest;
use App\Dto\SystemicQuestion\SystemicQuestionDisableRequest;
use App\Dto\SystemicQuestion\SystemicQuestionEnableRequest;
use App\Repository\DoctrineORMSystemicQuestionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Webmozart\Assert\Assert;

#[ApiResource(
    operations: [
        new Get(security: 'is_granted("SYSTEMIC_QUESTION_READ", object)'),
        new GetCollection(),
        new Post(
            uriTemplate: '/systemic_questions/create',
            status: 200,
            securityPostDenormalize: 'is_granted("ROLE_ADMIN") && is_granted("CLIENT_READ", object.client)',
            input: SystemicQuestionCreateRequest::class,
            output: SystemicQuestion::class,
            messenger: 'input'
        ),
        new Post(
            uriTemplate: '/systemic_questions/change',
            status: 200,
            securityPostDenormalize: 'is_granted("SYSTEMIC_QUESTION_EDIT", object.systemicQuestion) && is_granted("CLIENT_READ", object.client)',
            input: SystemicQuestionChangeRequest::class,
            output: SystemicQuestion::class,
            messenger: 'input'
        ),
        new Post(
            uriTemplate: '/systemic_questions/enable',
            status: 200,
            securityPostDenormalize: 'is_granted("ROLE_ADMIN") && is_granted("SYSTEMIC_QUESTION_EDIT", object.systemicQuestion)',
            input: SystemicQuestionEnableRequest::class,
            output: SystemicQuestion::class,
            messenger: 'input'
        ),
        new Post(
            uriTemplate: '/systemic_questions/disable',
            status: 200,
            securityPostDenormalize: 'is_granted("ROLE_ADMIN") && is_granted("SYSTEMIC_QUESTION_EDIT", object.systemicQuestion)',
            input: SystemicQuestionDisableRequest::class,
            output: SystemicQuestion::class,
            messenger: 'input'
        ),
    ],
    normalizationContext: ['groups' => ['systemicQuestion:read']],
)]
#[ORM\Table(name: 'systemic_question')]
#[ORM\Entity(repositoryClass: DoctrineORMSystemicQuestionRepository::class)]
class SystemicQuestion
{
    use TimestampableEntity;

    /** @Gedmo\Timestampable(on="create") **/
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected $createdAt; // phpcs:ignore

    /** @Gedmo\Timestampable(on="create") **/
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected $updatedAt; // phpcs:ignore

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue()]
    private int $id;

    #[ORM\Column(type: 'string', length: 4096)]
    private string $question = '';

    #[ORM\Column(type: 'boolean')]
    private bool $isEnabled;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'systemicQuestions')]
    private Client $client;

    private function __construct(string $question, Client $client)
    {
        Assert::minLength($question, 5);
        Assert::maxLength($question, 4096);
        $this->question = $question;
        $this->client = $client;
        $this->isEnabled = true;
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

    #[Groups(['systemicQuestion:read'])]
    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

    #[Groups(['systemicQuestion:read'])]
    public function getClient(): Client
    {
        return $this->client;
    }

    public function updateClient(Client $client): void
    {
        $this->client = $client;
    }

    public function enable(): void
    {
        $this->isEnabled = true;
    }

    public function disable(): void
    {
        $this->isEnabled = false;
    }

    #[Groups(['systemicQuestion:read'])]
    #[SerializedName('isEnabled')]
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    #[Groups(['systemicQuestion:read'])]
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    #[Groups(['systemicQuestion:read'])]
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function __toString(): string
    {
        return $this->question;
    }
}
