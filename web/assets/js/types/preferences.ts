/**
 * User preferences type definitions and default values.
 *
 * IMPORTANT: Keep these synchronized with PHP UserPreferencesSchema!
 */

export interface TablePreferences {
    columns: Record<string, boolean>;
    filters: Record<string, boolean>;
}

export interface UserPreferences {
    tables: {
        wayPoints: TablePreferences;
        walks: TablePreferences;
    };
}

/**
 * Default preferences schema.
 * Must match UserPreferencesSchema::defaults() in PHP.
 */
export const defaultPreferences: UserPreferences = {
    tables: {
        wayPoints: {
            columns: {
                locationName: true,
                malesCount: true,
                femalesCount: true,
                queerCount: true,
                peopleCount: true,
                note: true,
                oneOnOneInterview: true,
                wayPointTags: true,
                'walk.teamName': true,
                conceptOfDay: false,
                visitedAt: true,
                'walk.name': true,
            },
            filters: {
                wayPointTags: true,
                note: true,
                oneOnOneInterview: true,
                locationName: true,
                teamName: true,
                conceptOfDay: true,
                walkName: true,
                visitedAt: true,
            },
        },
        walks: {
            columns: {
                name: true,
                rating: true,
                startTime: true,
                endTime: true,
                peopleCount: true,
                teamName: true,
                isResubmission: true,
            },
            filters: {
                isResubmission: true,
                isUnfinished: true,
                name: true,
                teamName: true,
                guestNames: true,
                startTime: true,
            },
        },
    },
};

/**
 * Deep merges user preferences with defaults.
 * User preferences only contain overrides (deltas), so we merge them with defaults.
 *
 * @param defaults - The default preferences structure
 * @param overrides - User's preference overrides
 * @returns Complete preferences with defaults filled in
 */
export function mergePreferences(
    defaults: UserPreferences,
    overrides: Partial<UserPreferences>
): UserPreferences {
    return deepMerge(defaults, overrides) as UserPreferences;
}

/**
 * Deep merge utility function.
 */
function deepMerge<T>(target: T, source: Partial<T>): T {
    const output = {...target} as any;

    if (isObject(target) && isObject(source)) {
        Object.keys(source).forEach((key) => {
            const sourceValue = (source as any)[key];
            const targetValue = (target as any)[key];

            if (isObject(sourceValue) && isObject(targetValue)) {
                output[key] = deepMerge(targetValue, sourceValue);
            } else {
                output[key] = sourceValue;
            }
        });
    }

    return output;
}

function isObject(item: any): item is Record<string, any> {
    return item && typeof item === 'object' && !Array.isArray(item);
}

/**
 * Type guard for checking if preferences have the correct structure.
 */
export function isValidPreferences(prefs: any): prefs is UserPreferences {
    return (
        prefs &&
        typeof prefs === 'object' &&
        prefs.tables &&
        typeof prefs.tables === 'object'
    );
}
