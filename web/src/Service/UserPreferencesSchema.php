<?php
declare(strict_types=1);

namespace App\Service;

/**
 * Central schema definition for user preferences.
 * This defines the default structure and values for all user preferences.
 *
 * When adding new columns/filters:
 * 1. Add them to the defaults() method with a sensible default value
 * 2. Users will automatically see the new options without needing to re-save their preferences
 * 3. The merge logic in UserPreferencesResolver handles combining user overrides with new defaults
 */
final class UserPreferencesSchema
{
    /**
     * Returns the default preferences structure.
     *
     * Structure:
     * - tables: Contains preferences for different tables (wayPoints, walks)
     *   - columns: Which columns are visible (true) or hidden (false)
     *   - filters: Which filters are visible (true) or hidden (false)
     *
     * @return array{
     *     tables: array{
     *         wayPoints: array{
     *             columns: array<string, bool>,
     *             filters: array<string, bool>
     *         },
     *         walks: array{
     *             columns: array<string, bool>,
     *             filters: array<string, bool>
     *         }
     *     }
     * }
     */
    public static function defaults(): array
    {
        return [
            'tables' => [
                'wayPoints' => [
                    'columns' => [
                        'locationName' => true,
                        'malesCount' => true,
                        'femalesCount' => true,
                        'queerCount' => true,
                        'peopleCount' => true,
                        'note' => true,
                        'oneOnOneInterview' => true,
                        'wayPointTags' => true,
                        'walk.teamName' => true,
                        'visitedAt' => true,
                        'walk.name' => true,
                    ],
                    'filters' => [
                        'wayPointTags' => true,
                        'note' => true,
                        'oneOnOneInterview' => true,
                        'locationName' => true,
                        'walk.teamName' => true,
                        'walk.conceptOfDay' => true,
                        'walk.name' => true,
                        'visitedAt' => true,
                    ],
                ],
                'walks' => [
                    'columns' => [
                        'name' => true,
                        'rating' => true,
                        'startTime' => true,
                        'endTime' => true,
                        'peopleCount' => true,
                        'teamName' => true,
                        'isResubmission' => true,
                    ],
                    'filters' => [
                        'isResubmission' => true,
                        'isUnfinished' => true,
                        'name' => true,
                        'teamName' => true,
                        'guestNames' => true,
                        'startTime' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns all valid table names.
     *
     * @return list<string>
     */
    public static function getValidTables(): array
    {
        return \array_keys(self::defaults()['tables']);
    }

    /**
     * Returns all valid column names for a given table.
     *
     * @param string $table
     *
     * @return list<string>
     */
    public static function getValidColumnsForTable(string $table): array
    {
        $defaults = self::defaults();

        if (!isset($defaults['tables'][$table]['columns'])) {
            return [];
        }

        return \array_keys($defaults['tables'][$table]['columns']);
    }

    /**
     * Returns all valid filter names for a given table.
     *
     * @param string $table
     *
     * @return list<string>
     */
    public static function getValidFiltersForTable(string $table): array
    {
        $defaults = self::defaults();

        if (!isset($defaults['tables'][$table]['filters'])) {
            return [];
        }

        return \array_keys($defaults['tables'][$table]['filters']);
    }
}
