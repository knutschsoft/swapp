<?php
declare(strict_types=1);

namespace App\Handler\User;

use App\Dto\User\ChangeUserPreferencesRequest;
use App\Entity\UserPreferences;
use App\Repository\UserPreferencesRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ChangeUserPreferencesRequestHandler
{
    public function __construct(
        private UserPreferencesRepository $repository
    ) {
    }

    public function __invoke(ChangeUserPreferencesRequest $request): UserPreferences
    {
        $userPreferences = $request->user->getPreferences();

        if (!$userPreferences) {
            // Create new preferences with the provided overrides
            $userPreferences = new UserPreferences($request->user, $request->preferences);
            $userPreferences->assignToUser($request->user);
        } else {
            // PATCH semantics: merge the partial update with existing preferences
            $userPreferences->updatePartial($request->preferences);
        }

        $this->repository->save($userPreferences);

        return $userPreferences;
    }
}
