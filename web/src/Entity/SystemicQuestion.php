<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Dto\SystemicQuestion\SystemicQuestionChangeRequest;
use App\Dto\SystemicQuestion\SystemicQuestionCreateRequest;
use App\Dto\SystemicQuestion\SystemicQuestionDisableRequest;
use App\Dto\SystemicQuestion\SystemicQuestionEnableRequest;
use App\Repository\DoctrineORMSystemicQuestionRepository;
use App\Security\Voter\ClientVoter;
use App\Security\Voter\SystemicQuestionVoter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Webmozart\Assert\Assert;

#[ORM\Table(name: 'systemic_question')]
#[ORM\Entity(repositoryClass: DoctrineORMSystemicQuestionRepository::class)]
#[ApiResource(
    collectionOperations: [
    'get',
    'systemic_question_create' => [
        "messenger" => "input",
        "input" => SystemicQuestionCreateRequest::class,
        "output" => SystemicQuestion::class,
        "method" => "post",
        "status" => 200,
        "path" => "/systemic_questions/create",
        "security_post_denormalize" => 'is_granted("'.User::ROLE_ADMIN.'") && is_granted("'.ClientVoter::READ.'", object.client)',
    ],
    'systemic_question_change' => [
        "messenger" => "input",
        "input" => SystemicQuestionChangeRequest::class,
        "output" => SystemicQuestion::class,
        "method" => "post",
        "status" => 200,
        "path" => "/systemic_questions/change",
        "security_post_denormalize" => 'is_granted("'.SystemicQuestionVoter::EDIT.'", object.systemicQuestion) && is_granted("'.ClientVoter::READ.'", object.client)',
    ],
    'systemic_question_enable' => [
        "messenger" => "input",
        "input" => SystemicQuestionEnableRequest::class,
        "output" => SystemicQuestion::class,
        "method" => "post",
        "status" => 200,
        "path" => "/systemic_questions/enable",
        "security_post_denormalize" => 'is_granted("'.User::ROLE_ADMIN.'") && is_granted("'.SystemicQuestionVoter::EDIT.'", object.systemicQuestion)',
    ],
    'systemic_question_disable' => [
        "messenger" => "input",
        "input" => SystemicQuestionDisableRequest::class,
        "output" => SystemicQuestion::class,
        "method" => "post",
        "status" => 200,
        "path" => "/systemic_questions/disable",
        "security_post_denormalize" => 'is_granted("'.User::ROLE_ADMIN.'") && is_granted("'.SystemicQuestionVoter::EDIT.'", object.systemicQuestion)',
    ],
    ],
    itemOperations: [
    'get' => [
        'security' => 'is_granted("'.SystemicQuestionVoter::READ.'", object)',
    ],
    ],
    normalizationContext: ["groups" => ["systemicQuestion:read"]]
)]
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
