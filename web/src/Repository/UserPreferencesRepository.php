<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserPreferences;

interface UserPreferencesRepository
{
    /** @param mixed $id */
    public function findOneById(mixed $id): ?UserPreferences;

    public function save(UserPreferences $userPreferences): void;

    public function remove(UserPreferences $userPreferences): void;
}
