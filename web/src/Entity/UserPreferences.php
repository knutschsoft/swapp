<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Dto\User\ChangeUserPreferencesRequest;
use App\Security\Voter\UserPreferencesVoter;
use App\Security\Voter\UserVoter;
use App\Service\UserPreferencesResolver;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'user_preferences')]
#[ApiResource(
    operations: [
        new Get(
            securityPostDenormalize: 'is_granted("' . UserPreferencesVoter::READ . '", object)',
        ),
        new GetCollection(),
        new Post(
            uriTemplate: '/user_preferences/change',
            status: 200,
            securityPostDenormalize: 'is_granted("' . UserVoter::CHANGE_PREFERENCES . '", object.user)',
            input: ChangeUserPreferencesRequest::class,
            output: UserPreferences::class,
            messenger: 'input'
        ),
    ],
    normalizationContext: ['groups' => ['userPreferences:read']],
)]
class UserPreferences
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'preferences')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    /**
     * Stores only USER OVERRIDES (deltas) from defaults.
     * The full preferences are computed by merging with UserPreferencesSchema::defaults().
     *
     * @var array<array-key, mixed>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $preferencesData = [];

    /**
     * @param User                    $user
     * @param array<array-key, mixed> $preferences
     */
    public function __construct(User $user, array $preferences = [])
    {
        $this->user = $user;
        $this->preferencesData = $preferences;
    }

    #[ApiProperty(required: true, identifier: true)]
    #[Groups(['userPreferences:read'])]
    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Returns the stored preference overrides (deltas only).
     *
     * @return array<array-key, mixed>
     */
    public function getStoredPreferences(): array
    {
        return $this->preferencesData;
    }

    /**
     * Returns the effective preferences (defaults merged with user overrides).
     * This is what should be returned to the frontend.
     *
     * @return array<array-key, mixed>
     */
    #[Groups(['userPreferences:read'])]
    #[ApiProperty(required: true)]
    public function getPreferences(): array
    {
        $resolver = new UserPreferencesResolver();

        return $resolver->resolve($this->preferencesData);
    }

    /**
     * Updates the stored preferences using PATCH semantics.
     * Merges the partial update with existing overrides.
     *
     * @param array<array-key, mixed> $partialPreferences
     */
    public function updatePartial(array $partialPreferences): void
    {
        $this->preferencesData = $this->arrayMergeRecursive($this->preferencesData, $partialPreferences);
    }

    /**
     * Sets the preferences completely (replaces all overrides).
     * Use updatePartial() for PATCH semantics.
     *
     * @param array<array-key, mixed> $preferences
     */
    public function setPreferences(array $preferences): void
    {
        $this->preferencesData = $preferences;
    }

    public function assignToUser(User $user): void
    {
        $this->user = $user;
        $user->setPreferences($this);
    }

    /**
     * @param array<array-key, mixed> $array1
     * @param array<array-key, mixed> $array2
     *
     * @return array<array-key, mixed>
     */
    private function arrayMergeRecursive(array $array1, array $array2): array
    {
        foreach ($array2 as $key => $value) {
            // phpcs:disable SlevomatCodingStandard.ControlStructures.RequireTernaryOperator
            if (\is_array($value) && isset($array1[$key]) && \is_array($array1[$key])) {
                $array1[$key] = $this->arrayMergeRecursive($array1[$key], $value);
            } else {
                $array1[$key] = $value;
            }
            // phpcs:enable
        }

        return $array1;
    }
}
