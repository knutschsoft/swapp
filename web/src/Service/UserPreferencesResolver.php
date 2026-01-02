<?php
declare(strict_types=1);

namespace App\Service;

/**
 * Resolves user preferences by merging stored user overrides with default values.
 *
 * This is the key to the "migration-free" upgrade strategy:
 * - Users only store their DEVIATIONS from defaults
 * - New columns/filters appear automatically with their default values
 * - User customizations are preserved
 *
 * Example:
 * Default: ['columns' => ['name' => true, 'date' => true, 'newCol' => true]]
 * User stored: ['columns' => ['date' => false]]
 * Result: ['columns' => ['name' => true, 'date' => false, 'newCol' => true]]
 *                                        ↑ user override    ↑ new default appears!
 */
final class UserPreferencesResolver
{
    /**
     * Resolves effective preferences by merging user overrides with defaults.
     *
     * @param array<array-key, mixed> $storedPreferences User's stored preference overrides
     *
     * @return array<array-key, mixed> Complete preferences with defaults filled in
     */
    public function resolve(array $storedPreferences): array
    {
        $defaults = UserPreferencesSchema::defaults();

        return $this->mergeRecursive($defaults, $storedPreferences);
    }

    /**
     * Validates that stored preferences only contain valid keys from the schema.
     * Returns an array of invalid paths found.
     *
     * @param array<string, mixed> $storedPreferences
     *
     * @return list<string> Array of invalid paths (e.g., ['tables.wayPoints.columns.invalidColumn'])
     */
    public function findInvalidKeys(array $storedPreferences): array
    {
        $defaults = UserPreferencesSchema::defaults();
        $invalidPaths = [];

        $this->validateKeysRecursive($storedPreferences, $defaults, '', $invalidPaths);

        return $invalidPaths;
    }

    /**
     * Recursively merges user overrides into defaults.
     *
     * Rules:
     * - If a key exists in both arrays and both values are arrays: recurse
     * - If a key exists in overrides: use override value
     * - If a key only exists in defaults: use default value
     *
     * @param array<array-key, mixed> $defaults
     * @param array<array-key, mixed> $overrides
     *
     * @return array<array-key, mixed>
     */
    private function mergeRecursive(array $defaults, array $overrides): array
    {
        foreach ($overrides as $key => $value) {
            // phpcs:disable SlevomatCodingStandard.ControlStructures.RequireTernaryOperator
            if (\is_array($value) && isset($defaults[$key]) && \is_array($defaults[$key])) {
                // Both are arrays: merge recursively
                $defaults[$key] = $this->mergeRecursive($defaults[$key], $value);
            } else {
                // Override takes precedence
                $defaults[$key] = $value;
            }
        }

        return $defaults;
    }

    /**
     * @param array<array-key, mixed> $data
     * @param array<array-key, mixed> $schema
     * @param string               $path
     * @param list<string>         $invalidPaths
     */
    // phpcs:disable SlevomatCodingStandard.PHP.DisallowReference.DisallowedPassingByReference
    private function validateKeysRecursive(array $data, array $schema, string $path, array &$invalidPaths): void
    {
        foreach ($data as $key => $value) {
            $currentPath = $path === '' ? (string) $key : $path . '.' . $key;

            if (!\array_key_exists($key, $schema)) {
                $invalidPaths[] = $currentPath;
                continue;
            }

            if (\is_array($value) && \is_array($schema[$key])) {
                $this->validateKeysRecursive($value, $schema[$key], $currentPath, $invalidPaths);
            }
        }
    }
}
